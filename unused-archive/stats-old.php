<?php
	//temp {
		$time=microtime();
		$time=explode(' ',$time);
		$time=$time[1]+$time[0];
		$start=$time;

		error_reporting(E_ALL);
		ini_set('display_errors',1);

		//$connection=mysql_connect($hostname,$username,$password,$database);
		//if(!$connection)
			//die('error: failed to connect to MySQL: '.mysql_error());
		//mysql_select_db($database,$connection);
		//mysql_set_charset('utf8',$connection);
	//temp }

	if(isset($_GET['img'])){
		$height=3;
		$gap=10;

		include('data-tid-genre.php');
		//$bin=array();
		$start=key($tid);
		end($tid);
		$end=key($tid);

		$my_img = imagecreate( 1000, ceil(($end-$start)/1000)*$height+$gap );

		$background = imagecolorallocate( $my_img, 0, 0, 0 );

		$dnb		= imagecolorallocate($my_img,0,255,0);
		$dubstep	= imagecolorallocate($my_img,255,255,0);
		$other		= imagecolorallocate($my_img,191,191,191);

		//imagesetthickness ( $my_img, 5 );

		$offset_y=0;
		for($i=$start;$i<=$end;$i++){
			if($i%1000==0)
				$offset_y+=$height;

			if($i==8020050){
				$offset_y+=$gap;
			}
			$color=(!array_key_exists($i,$tid) ? $background : ($tid[$i]=='Drum & Bass' ? $dnb : ($tid[$i]=='Dubstep' ? $dubstep : $other)));
			imageline($my_img, $i%1000,$offset_y,$i%1000,$offset_y+$height, $color);
		}

		header( "Content-type: image/png" );
		imagepng( $my_img );
		imagecolordeallocate( $line_color );
		imagecolordeallocate( $background );
		imagedestroy( $my_img );

		die();
	}
?>
<style>
	b{float:left;display:inline-block;width:1px;height:3px;}
	b:nth-child(1000n){clear:left;}
	.y{background:#0f0;}
	.n{background:#000;}
	.dnb{background:#0f0;}
	.dubstep{background:#ff0;}
	.other{background:#aaa;}
	hr{display:block;border:0;width:100%;height:1px;background:#eee;clear:both;margin:5px 0;float:left;}
</style>
<?
	if(!isset($_GET['genres'])){
		include('data-tid.php');

		//$result=mysql_query("SELECT id FROM tid WHERE id>".end($tid));

		//while($row=mysql_fetch_array($result,MYSQL_ASSOC)){
			//$tid[]=$row['id'];
		//}

		//$full=range(8020050,8042025);
		$bin=array();

		for($i=7887000;$i<=end($tid);$i++){
			if($i==8020050)
				$bin[]='<hr/>';
			$bin[]='<b class="'.(array_key_exists($i,$tid) ? 'y' : 'n').'"></b>';
		}
		echo join('',$bin);
	}else{
		include('data-tid-genre.php');

		//echo count($tid);

		//Electronica
		//House / Electro
		//Deep / Tech House
		//Techno
		//Hardstyle
		//Drum & Bass
		//Dubstep
		//Trance
		//Breaks
		//Hard Dance
		//Hardcore

		$bin=array();
		end($tid);
		$end=key($tid);
		for($i=7887000;$i<=$end;$i++){
			if($i==8020050){
				$bin[]='<hr/>';
			}
			$bin[]='<b class="'.(!array_key_exists($i,$tid) ? 'n' : ($tid[$i]=='Drum & Bass' ? 'dnb' : ($tid[$i]=='Dubstep' ? 'dubstep' : 'other'))).'"></b>';
		}
		echo join('',$bin);
	}

	$time=microtime();
	$time=explode(' ',$time);
	$time=$time[1]+$time[0];
	$finish=$time;
	$total_time=round(($finish-$start),4);
	echo '<div style="clear:both;padding-top:15px">Page generated in '.$total_time.' seconds.</div>';
?>