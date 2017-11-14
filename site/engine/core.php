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
	include_once('../../db-config.php');

	$connection=mysql_connect($hostname,$username,$password,$database);
	if(!$connection)
		die('error: failed to connect to MySQL: '.mysql_error());
	mysql_select_db($database,$connection);
	mysql_set_charset('utf8',$connection);

	function n($x){return number_format($x,0,'.',' ');}

	ini_set('max_execution_time',60);

	/* ————————————————————————————————————————————————————————————————— */

	$range=10;
	$type_is_simple=false;

	if(!isset($_GET['mode']))	die('error: no mode');
	//if(!isset($_GET['drop']))	die('error: no drop');
	if(!isset($_GET['offset']))	die('error: no offset');

	//$drop	= intval($_GET['drop']);
	$offset	= intval($_GET['offset']);
	//if($drop<0)		die('error: bad drop');
	if($offset<0)	die('error: bad offset');

	$result=mysql_query('SELECT COUNT(*) AS rows,MIN(id) AS min,MAX(id) AS max FROM tid');
	if(!mysql_num_rows($result))die('error: empty result');
	$row=mysql_fetch_assoc($result);
	$min=$row['min'];//copy file before
	//$min=min($row['min'],intval(file_get_contents('min.txt')));
	$max=$row['max'];
	$rows_number=$row['rows'];

	include('lib/ganon.php');
	include('lib/requests.php');

	function url($id){
		return 'http://www.trackitdown.net/track/artist/title/genre/'.$id.'.html';
	}
	function list_ids($from,$range){
		$ids=array();
		for($i=$from;$i<$from+$range;$i++){
			$ids[]=$i;
		}
		return $ids;
	}
	function list_urls_from_ids($ids){
		$urls=array();
		foreach($ids as $i){
			$urls[]=url($i);
		}
		return $urls;
	}
	function list_urls($from,$range){
		return list_urls_from_ids(list_ids($from,$range));
	}

	$mode=$_GET['mode'];
	$urls=array();

	$offset_delta=$range;
	switch($mode){
		case 'recheck':
				$from=$max-$offset-$range;
				while(count($urls)<$range){
					$ids=list_ids($from,$range);
					$from-=$range;
					$result=mysql_query('SELECT id FROM tid WHERE id IN ('.join(',',$ids).')');
					while($row=mysql_fetch_assoc($result)){
						$id=intval($row['id']);
						$pos=array_search($id,$ids);
						if($pos!==false){
							unset($ids[$pos]);
						}
					}
					$urls=array_merge($urls,list_urls_from_ids($ids));
				}
				$offset_delta=$max-$offset-$from;
				/*$start=$max-1-$offset;for($i=0;$i<$range;$i++)$urls[]=url($start-$i);*/
			break;
		case 'incremental':
				$urls=list_urls($max+1+$offset,$range);
				/*$start=$max+1+$offset;for($i=0;$i<$incremental_steps;$i++)$urls[]=url($start+$i);*/
			break;
		case 'deepscan':
				//забудь
				//$urls=list_urls($min-$range,$range);
				/*$start=$min-1;for($i=0;$i<$range;$i++)$urls[]=url($start-$i);*/
				//$min-=$range;
				//$save_min=true;
			break;
		default:die('error: bad mode');break;
	}

	function save_release($html,$id){
		$box=$html('#mainContentForPage',0);

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
			return $result!==false;
		}
		return false;
	}
	function id_from_url($url){
		preg_match("/\/(\d+)\.html/",$url,$matches);
		$id=$matches[1];
		return $id;
	}

	$success_urls=array();
	if($type_is_simple){
		foreach($urls as $url){
			try{
				if(save_release(file_get_dom($url),id_from_url($url)))
					$success_urls[]=$url;
			}catch(Exception $e){}
		}
	}else{
		$callback=function($data,$info){
			global $success_urls;
			if($info['http_code']==200){
				$url=$info['url'];
				if(save_release(str_get_dom($data),id_from_url($url)))
					$success_urls[]=$url;
			}
		};

		$requests = new Requests();
		$requests->process($urls,$callback);
	}

	echo '({success_number:'.count($success_urls).','
		  .'success_urls:\''.join(',',$success_urls).'\','
		  .'all_urls:\''.join(',',$urls).'\','
		  .'offset_delta:'.$offset_delta.','
		  .'max:'.$max.'})';




	if(isset($save_min))file_put_contents('min.txt',$min);

	$time=microtime();
	$time=explode(' ',$time);
	$time=$time[1]+$time[0];
	$finish=$time;
	$total_time=round(($finish-$start),4);
	//echo 'Page generated in '.$total_time.' seconds.'."\n";