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
		//moved to table-template.html
	}
?>
<!-- //dnb//(<?php echo count($dnb); ?>, +<?php echo $recent_dnb; ?>) -->
<!-- //dubstep//(<?php echo count($dubstep); ?>, +<?php echo $recent_dubstep; ?>) -->

<div class="tid-table tid-table-dnb"
   tid-table-template="dnb">
</div>
<div class="tid-table tid-table-dubstep"
   tid-table-template="dubstep">
</div>

<?php die(); ?>