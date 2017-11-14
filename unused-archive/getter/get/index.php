<?php
	//temp {
		$time=microtime();
		$time=explode(' ',$time);
		$time=$time[1]+$time[0];
		$start=$time;

		error_reporting(E_ALL);
		ini_set('display_errors',1);

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
	//temp }
?>
	<!DOCTYPE html>
	<html>
		<head>
			<title>tid</title>
		</head>
		<body>
			<style>
				html,body,table{font-family:Tahoma;font-size:12px;}
				table{border-collapse:collapse;}
				td{border:1px solid #eee;padding:5px 10px;}
			</style>
<?php
	if(isset($_GET['offset']))
		echo '<a href="/tid/get/?offset='.(intval($_GET['offset'])+1).'">/tid/get/?offset='.(intval($_GET['offset'])+1).'</a><br/>';

	include('ganon.php');
	$backward=isset($_GET['backward']);
	$result=mysql_query('SELECT COUNT(*) AS rows,MIN(id) AS min,MAX(id) AS max FROM tid');

	if(!mysql_num_rows($result))
		die('error');
	else
		{
			$row=mysql_fetch_assoc($result);
			$min=$row['min'];
			$max=$row['max'];
			if($backward)
				$id=$min;
			else
				$id=$max;
			$id += (isset($_GET['offset']) ? intval($_GET['offset']) : 1) * ($backward ? -1 : 1);
			$rows=$row['rows'];
		}
	function n($x){
		return number_format($x,0,'.',' ');
	}

	echo n($rows).' / '.n($max-$min).' ('.n($min).' – '.n($max).')<br/>'; ?>
<?php
	ini_set('max_execution_time',60*10);
	$steps=10;

	$data=array();
	for($i=0;$i<$steps;$i++){
		$url='https://www.trackitdown.net/track/artist/title/genre/'.($id+($backward ? -$i : $i)).'.html';
		//echo $url;
		$html=file_get_dom($url);
		$box=$html('#mainContentForPage',0);

		$title		=$box('h1',0)->getPlainText();
		$artist		=$box('#trackDetailArtistName',0)->getPlainText();
		$genre		=$box('#trackDetailGenreName',0)->getPlainText();
		$label		=$box('#trackDetailRecordlabelName',0)->getPlainText();
		$date		=$box('#trackDetailReleaseDate',0)->getPlainText();
		$catalog_id	=$box('#trackDetailCatalogueNo',0);
		if(is_null($catalog_id))
			$catalog_id='';
		else
			$catalog_id = $catalog_id->getPlainText();

		$last_el=end($data);
		if($last_el['title']!=$title
			|| $last_el['artist']!=$artist
			|| $last_el['genre']!=$genre
			|| $last_el['label']!=$label
			|| $last_el['date']!=$date
			|| $last_el['catalog_id']!=$catalog_id){
				$data[]=array(
					'id'			=> ($id+($backward ? -$i : $i)),
					'title'			=> $title,
					'artist'		=> $artist,
					'genre'			=> $genre,
					'label'			=> $label,
					'date'			=> $date,
					'catalog_id'	=> $catalog_id
				);

				$title		="'".mysql_real_escape_string(trim($title))."'";
				$artist		="'".mysql_real_escape_string(trim($artist))."'";
				$genre		="'".mysql_real_escape_string(trim($genre))."'";
				$label		="'".mysql_real_escape_string(trim($label))."'";
				$date		="'".mysql_real_escape_string(trim($date))."'";
				$catalog_id	="'".mysql_real_escape_string(trim($catalog_id))."'";

				$result=mysql_query("INSERT INTO tid(id,title,artist,genre,label,`date`,catalog_id,added) VALUES ("
									.($id+($backward ? -$i : $i)).",$title,$artist,$genre,$label,$date,$catalog_id,NOW())");
		}
	}

	?>
			<table>
				<thead>
					<tr>
						<td>id</td>
						<td>title</td>
						<td>artist</td>
						<td>genre</td>
						<td>label</td>
						<td>date</td>
						<td>catalog id</td>
					</tr>
				</thead>
				<tbody>
					<?php $id=1;foreach($data as &$track){ ?>
						<tr>
							<td><?php echo $track['id']; ?></td>
							<td><?php echo $track['title']; ?></td>
							<td><?php echo $track['artist']; ?></td>
							<td><?php echo $track['genre']; ?></td>
							<td><?php echo $track['label']; ?></td>
							<td><?php echo $track['date']; ?></td>
							<td><?php echo $track['catalog_id']; ?></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</body>
	</html>
	<?php
		$time=microtime();
		$time=explode(' ',$time);
		$time=$time[1]+$time[0];
		$finish=$time;
		$total_time=round(($finish-$start),4);
		echo 'Page generated in '.$total_time.' seconds.'."\n";
	?>