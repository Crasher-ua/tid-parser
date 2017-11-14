<?php
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

	$result=mysql_query('SELECT COUNT(*) AS rows,MAX(id) AS max FROM tid');
	$row=mysql_fetch_assoc($result);

	$number=$row['rows'];
	$max=$row['max'];

	echo 'number: '.number_format($number,0,'.',' ').'<br/>'
		.'max id: '.number_format($max,0,'.',' ').'<br/>'
		.'percent: '.number_format($number/$max*100,2,'.',' ').'%';