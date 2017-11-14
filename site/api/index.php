<?php
	$hostname='';
	$username='';
	$password='';
	$database='';
	include_once('../../db-config.php');

	$connection=mysql_connect($hostname,$username,$password,$database);
	if(!$connection)
		die('error: failed to connect to MySQL: '.mysql_error());
	mysql_select_db($database,$connection);
	mysql_set_charset('utf8',$connection);

	$result=mysql_query("SELECT *"
						.",IF(YEAR(STR_TO_DATE(`date`,'%M %d,%Y'))>YEAR(CURDATE()),YEAR(STR_TO_DATE(`date`,'%M %d,%Y')),DATE_FORMAT(STR_TO_DATE(`date`,'%M %d,%Y'),'%d/%m')) AS date_simple"
						.",IF(DATEDIFF(CURDATE(),`added`)<2,1,0) AS recent"
						." FROM tid WHERE (genre='Drum & Bass' OR genre='Dubstep')"
						." AND YEAR(STR_TO_DATE(`date`,'%M %d,%Y'))>2014"
						." AND DATEDIFF(CURDATE(),STR_TO_DATE(`date`,'%M %d,%Y'))<=1"
						." ORDER BY genre,STR_TO_DATE(`date`,'%M %d,%Y')");

	$dnb=array();
	$recent_dnb=0;
	$dubstep=array();
	$recent_dubstep=0;

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
		$data[]=$row;
	}

	function table($src){
		?>
			<table class="table">
				<thead>
					<tr>
						<th>#</th>
						<th>artist</th>
						<th>title</th>
						<th>label</th>
						<th>date</th>
						<th>catalog</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($src as $row){ ?>
						<tr<?php if($row['recent'])echo ' class="recent"'; ?>>
							<td><a href="https://www.trackitdown.net/track/artist/title/genre/<?php echo $row['id']; ?>.html" target="_blank" title="<?php echo $row['id']; ?>"><span class="dashicons dashicons-share-alt2"></span></a></td>
							<td><?php echo str_replace(' featuring ',' ft. ',$row['artist']); ?></td>
							<td><?php echo str_replace(array(' (Original Mix)',' - Original Mix',' (Original)',' (original)'),'',$row['title']); ?></td>
							<td class="mlabel"><?php echo $row['label']; ?></td>
							<td><?php echo $row['date_simple']; ?></td>
							<td>
								<?php $c=$row['catalog_id'];$catalog_max_length=99; ?>
								<span title="<?php echo $c; ?>"><?php echo (strlen($c)>$catalog_max_length ? substr($c,-9) . '…':$c); ?></span>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		<?php
	}
?>
<!-- //dnb//(<?php echo count($dnb); ?>, +<?php echo $recent_dnb; ?>) -->
<!-- //dubstep//(<?php echo count($dubstep); ?>, +<?php echo $recent_dubstep; ?>) -->

<div class="tid-table tid-table-dnb">
	<?php table($dnb); ?>
</div>
<div class="tid-table tid-table-dubstep">
	<?php table($dubstep); ?>
</div>

<?php die(); ?>