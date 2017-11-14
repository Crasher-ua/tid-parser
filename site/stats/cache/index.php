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
	include_once('../../../db-config.php');

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

	$result=mysql_query('SELECT id,genre FROM tid ORDER BY id');
	$str='';
	while($row=mysql_fetch_assoc($result)){
		$str .= $row['id'].'=>'."'".$row['genre']."',";
	}
	$str='<?php $tid=array('.$str.');';
	file_put_contents('../data-tid-genre.php',$str);
	echo mysql_num_rows($result).'<br/>'.strlen($str).'<br/>';
?>

<?php
	$time=microtime();
	$time=explode(' ',$time);
	$time=$time[1]+$time[0];
	$finish=$time;
	$total_time=round(($finish-$start),4);
	echo 'Page generated in '.$total_time.' seconds.'."\n";
?>
	</body>
</html>