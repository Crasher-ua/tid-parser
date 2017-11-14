<?php
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
?>
<!DOCTYPE html>
<html>
	<head>
		<title>tid</title>
	</head>
	<body>
<?php
	ini_set('max_execution_time',60*10);
	$POWER_DOWN=100;
	$POWER_UP=0;

	include('ganon.php');
	include('requests.php');

	$min_from_db=isset($_GET['min_from_db']);

	function n($x){
		return number_format($x,0,'.',' ');
	}

	$result=mysql_query('SELECT COUNT(*) AS rows,MIN(id) AS min,MAX(id) AS max FROM tid');
	$row=mysql_fetch_assoc($result);
	echo n($row['rows']).'<br/>';

	$urls = array();

	/* минимальное id сохраняется и берётся с файла, чтобы повторно не переспрашивать id
	   страницы с 404 и не зациклится на промежутках, больших, чем шаг опроса.
	   проверка минимума сделана, так как другой getter может продвинуться "глубже" */
	$id = $row['min'];
	if(!$min_from_db)
		$id = min($id,intval(file_get_contents('min.txt')));
	for($i=1;$i<=$POWER_DOWN;$i++)
		$urls[]='http://www.trackitdown.net/track/artist/title/genre/'.($id-$i).'.html';
	$min=$id-$POWER_DOWN;
	if($min<0)$min=0;

	/* максимальное id не сохраняется и не берётся с базы данных, так как id могут появится
	   с последующими постами, поэтому перепроверка на месте нужна всегда */
	$id = $row['max'];
	for($i=1;$i<=$POWER_UP;$i++)
		$urls[]='http://www.trackitdown.net/track/artist/title/genre/'.($id+$i).'.html';
	$max=$id+$POWER_UP;

	global $saved_urls;
	$saved_urls=array();

	$callback=function($data,$info){
		global $saved_urls;
		//print "Callback: ";
		//echo $info['http_code'].'<br/>';
		//print_r($data);
		//print_r($info);
		if(!in_array($info['url'],$saved_urls) && $info['http_code']==200){
			$saved_urls[]=$info['url'];

			$html=str_get_dom($data);
			$box=$html('#mainContentForPage',0);

			preg_match("/\/(\d+)\.html/",$info['url'],$matches);
			$id			=$matches[1];
			echo n($id)." / ";

			echo '<!--';
			var_dump($box('h1',0)->getPlainText());
			echo '-->';

			if(!is_null($box('h1',0)) && !is_null($box('#trackDetailArtistName',0))){
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

				$title		="'".mysql_real_escape_string(trim($title))."'";
				$artist		="'".mysql_real_escape_string(trim($artist))."'";
				$genre		="'".mysql_real_escape_string(trim($genre))."'";
				$label		="'".mysql_real_escape_string(trim($label))."'";
				$date		="'".mysql_real_escape_string(trim($date))."'";
				$catalog_id	="'".mysql_real_escape_string(trim($catalog_id))."'";

				$result=mysql_query("INSERT INTO tid(id,title,artist,genre,label,`date`,catalog_id,added) VALUES ($id,$title,$artist,$genre,$label,$date,$catalog_id,NOW())");
			}
		}
	};

	$requests = new Requests();
	$requests->process($urls,$callback);
?>

<?php
	$result=mysql_query('SELECT COUNT(*) AS rows,MIN(id) AS min,MAX(id) AS max FROM tid');
	$row=mysql_fetch_assoc($result);
	//$min=$row['min'];
	//$max=$row['max'];
	echo "<br/>rows: ".n($row['rows']).' / '.n($max-$min).' ('.n($min).'-'.n($max).')'.'<br/>';

	$time=microtime();
	$time=explode(' ',$time);
	$time=$time[1]+$time[0];
	$finish=$time;
	$total_time=round(($finish-$start),4);
	echo 'Page generated in '.$total_time.' seconds.'."\n";
	//echo '<script>setTimeout(function(){window.location.href=window.location.href},1000)</script>';
	file_put_contents('min.txt',$min);
?>
	</body>
</html>