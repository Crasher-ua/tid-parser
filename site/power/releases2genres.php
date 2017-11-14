<?php
	include_once('config.php');
	if($errors){
		error_reporting(E_ALL);
		ini_set('display_errors',1);
	}

	if($limit_days>0){
		$current_date=new DateTime();
		function filter_date($var){
			global $current_date,$limit_days;
			$row_date=new DateTime($var['date']);
			return (($current_date->format('U')-$row_date->format('U'))/(60*60*24) < $limit_days);
		}
		$releases=array_filter($releases,'filter_date');
	}

	if($lets_play_database && $limit_days){
		$connection=mysql_connect($hostname,$username,$password,$database);
		if(!$connection)
			die('error: failed to connect to MySQL: '.mysql_error());
		mysql_select_db($database,$connection);
		mysql_set_charset('utf8',$connection);

		$releases_db=array();
		$result=mysql_query("SELECT * FROM tid WHERE "
							."DATEDIFF(CURDATE(),`added`)<".($limit_days+1)
							." AND (genre='".join("' OR genre='",$our_genres_tid)."')"
							." AND YEAR(STR_TO_DATE(`date`,'%M %d,%Y'))>2014"
							//." AND DATEDIFF(CURDATE(),STR_TO_DATE(`date`,'%M %d,%Y'))<=1"
							." ORDER BY genre,STR_TO_DATE(`date`,'%M %d,%Y') DESC");
		while($row=mysql_fetch_assoc($result)){
			$releases_db[$row['id']]=$row;
		}
		foreach($releases as &$release)
			if(isset($releases_db[$release['id']])){
				$info_db=$releases_db[$release['id']];
				$release['author']=$info_db['artist'];
				$release['track']=str_replace(array(' (Original Mix)',' - Original Mix',' (Original)',' (original)'),'',$info_db['title']);
				$release['release_date']=$info_db['date'];
				$release['label']=$info_db['label'];
				$release['catalog_id']=$info_db['catalog_id'];
			}
	}

	foreach($releases as $release){
		$genre=$release['genre'];
		if(!isset($GLOBALS[$genre])){
			$GLOBALS[$genre]=array();
		}
		$GLOBALS[$genre][]=$release;
	}

	function table($releases){ ?>
		<table class="table">
			<thead>
				<tr>
					<th class="text-center" width="90"><span class="glyphicon glyphicon-picture"></span>&nbsp;Cover</th>
					<th class="text-center"><span class="glyphicon glyphicon-calendar"></span></th>
					<th>Label</th>
					<th class="text-center">#&nbsp;<span class="glyphicon glyphicon-folder-open"></span></th>
					<th class="text-center"><span class="glyphicon glyphicon-user"></span>&nbsp;Title</th>
					<th class="text-center">#</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$releases_count=count($releases);
					$rows_left=0;
					$rows=1;
				?>
				<?php for($i=0;$i<$releases_count;$i++): ?>
					<?php
						if($rows_left==0){
							$rows=1;
							while($releases[$i]['img']!='' && ($i+$rows_left+1<$releases_count) && $releases[$i+$rows_left]['img']==$releases[$i]['img']){
								$rows_left++;
								$rows++;
							}
						}
					?>
					<tr>
						<?php if($rows==0): ?>
							<?php $rows_left--; ?>
						<?php elseif($rows==1): ?>
							<td><img width="50" height="50" src="<?php echo $releases[$i]['img']; ?>"/></td>
						<?php else: ?>
							<?php $rows_left--; ?>
							<td rowspan="<?php echo $rows-1; ?>"><img width="50" height="50" src="<?php echo $releases[$i]['img']; ?>"/></td>
							<?php $rows=0; ?>
						<?php endif; ?>
						<td>
							<?php if(isset($releases[$i]['release_date'])): ?>
								<?php echo $releases[$i]['release_date']; ?>
							<?php endif; ?>
						</td>
						<td>
							<?php if(isset($releases[$i]['label'])): ?>
								<?php echo $releases[$i]['label']; ?>
							<?php endif; ?>
						</td>
						<td>
							<?php if(isset($releases[$i]['catalog_id'])): ?>
								<?php echo $releases[$i]['catalog_id']; ?>
							<?php endif; ?>
						</td>
						<td>
							<?php echo $releases[$i]['author']; ?> — <?php echo $releases[$i]['track']; ?>
						</td>
						<td><a target="_blank" href="https://www.trackitdown.net/track/artist/title/genre/<?php echo $releases[$i]['id']; ?>.html"><?php echo $releases[$i]['id']; ?></a></td>
					</tr>
				<?php endfor; ?>
			</tbody>
		</table>
	<?php }