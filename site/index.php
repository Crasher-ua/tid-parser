<?php
	$hostname='';
	$username='';
	$password='';
	$database='';
	include_once('db-config.php');

	$connection=mysql_connect($hostname,$username,$password,$database);
	if(!$connection)
		die('error: failed to connect to MySQL: '.mysql_error());
	mysql_select_db($database,$connection);
	mysql_set_charset('utf8',$connection);

	$result=mysql_query("SELECT *,IF(DATEDIFF(CURDATE(),`added`)<2,1,0) AS recent FROM tid WHERE (genre='Drum & Bass' OR genre='Dubstep' OR genre='Breaks') AND YEAR(STR_TO_DATE(`date`,'%M %d,%Y'))>2014 AND DATEDIFF(CURDATE(),STR_TO_DATE(`date`,'%M %d,%Y'))<=1 ORDER BY genre,STR_TO_DATE(`date`,'%M %d,%Y') DESC");

	$dnb=array();
	$recent_dnb=0;
	$dubstep=array();
	$recent_dubstep=0;
	$breaks=array();
	$recent_breaks=0;

	$data=array();
	while($row=mysql_fetch_assoc($result)){
		if($row['genre']=='Drum & Bass'){
			$dnb[]=$row;
			if($row['recent'])
				$recent_dnb++;
		}
		if($row['genre']=='Dubstep'){
			$dubstep[]=$row;
			if($row['recent'])
				$recent_dubstep++;
		}
		if($row['genre']=='Breaks'){
			$breaks[]=$row;
			if($row['recent'])
				$recent_breaks++;
		}
		$data[]=$row;
	}

	function table($src){
		?>
			<table class="table">
				<thead>
					<tr>
						<th>#</th>
						<th><span class="glyphicon glyphicon-user"></span></th>
						<th>title</th>
						<th>label</th>
						<th><span class="glyphicon glyphicon-calendar"></span></th>
						<th># <span class="glyphicon glyphicon-folder-open"></span></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($src as $row){ ?>
						<tr<?php if($row['recent'])echo' class="success"'; ?>>
							<td><a href="https://www.trackitdown.net/track/artist/title/genre/<?php echo $row['id']; ?>.html" target="_blank"><?php echo $row['id']; ?></a></td>
							<td><?php echo $row['artist']; ?></td>
							<td><?php echo str_replace(array(' (Original Mix)',' - Original Mix',' (Original)',' (original)'),'',$row['title']); ?></td>
							<td class="mlabel"><?php echo $row['label']; ?></td>
							<td><?php echo $row['date']; ?></td>
							<td><?php echo $row['catalog_id']; ?></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		<?php
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>TID</title>
		<link rel="stylesheet" href="lib/bootstrap.css">
		<script src="lib/jquery.js"></script>
		<script src="lib/bootstrap.js"></script>
		<script src="lib/jquery.cookie.js"></script>

		<script src="core.js?v1"></script>
		<link rel="stylesheet" href="style.css?v1">
	</head>
	<body>
		<div class="container">
			<h1 class="header">TID</h1>

			<div class="alert alert-danger" id="alert-main">
				<p><span class="glyphicon glyphicon-warning-sign"></span> <b>Пора сваливать, ребята!</b> — скажет однажды Ярик, ведь эта админка не поддерживается. Во имя безопасности и удобства, развивается исключительно отображение TID в <a href="http://displace.audio/wp-admin/">админке сайта</a>.</p>
				<p>Напишите мне в личку, если это чем-то ущемляет удобства.</p>
			</div>
			<br/>

			<ul id="myTab" class="nav nav-tabs" role="tablist">
				<li role="presentation" class="active"><a href="#dnb" id="dnb-tab" role="tab" data-toggle="tab" aria-controls="dnb" aria-expanded="true">Drum & Bass<?php if($recent_dnb)echo' <span class="badge">+'.$recent_dnb.'</span>'; ?></a></li>
				<li role="presentation" class=""><a href="#dubstep" role="tab" id="dubstep-tab" data-toggle="tab" aria-controls="dubstep" aria-expanded="false">Dubstep<?php if($recent_dubstep)echo' <span class="badge">+'.$recent_dubstep.'</span>'; ?></a></li>
				<li role="presentation" class=""><a href="#breaks" role="tab" id="breaks-tab" data-toggle="tab" aria-controls="breaks" aria-expanded="false">Breaks<?php if($recent_breaks)echo' <span class="badge">+'.$recent_breaks.'</span>'; ?></a></li>
				<li role="presentation" class=""><a href="#settings" role="tab" id="settings-tab" data-toggle="tab" aria-controls="settings" aria-expanded="false"><span class="glyphicon glyphicon-cog"></span> Настройки</a></li>
			</ul>

			<div class="tab-content">
				<div role="tabpanel" class="tab-pane fade active in" id="dnb" aria-labelledby="dnb-tab">
					<?php table($dnb); ?>
				</div>
				<div role="tabpanel" class="tab-pane fade" id="dubstep" aria-labelledby="dubstep-tab">
					<?php table($dubstep); ?>
				</div>
				<div role="tabpanel" class="tab-pane fade" id="breaks" aria-labelledby="breaks-tab">
					<?php table($breaks); ?>
				</div>
				<div role="tabpanel" class="tab-pane fade" id="settings" aria-labelledby="settings-tab">
					<br/>
					<div class="row">
						<div class="col-xs-8">
							<div class="checkbox">
								<label>
									<input type="checkbox" id="show-only-new"> только новые релизы
								</label>
							</div>
							<p id="labels-to-remove">Спрятанные лейблы: <span class="list"></span></p>
						</div>
						<div class="col-xs-4">
							<div class="panel panel-default">
								<div class="panel-heading">
									<h3 class="panel-title">Статистика</h3>
								</div>
								<div class="panel-body">
									<dl class="dl-horizontal">
										<dt>Drum & Bass</dt>
										<dd><?php echo count($dnb); ?> (+<?php echo $recent_dnb; ?>)</dd>
										<dt>Dubstep</dt>
										<dd><?php echo count($dubstep); ?> (+<?php echo $recent_dubstep; ?>)</dd>
										<dt>Breaks</dt>
										<dd><?php echo count($breaks); ?> (+<?php echo $recent_breaks; ?>)</dd>
									</dl>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</body>
</html>