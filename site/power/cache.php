<?php
	include_once('config.php');

	if(!file_exists($cache_url)){
		$have_to_cache=true;
		include_once('releases.php');
	}

	$json=file_get_contents($cache_url);
	$array=json_decode($json,TRUE);
	$releases=$array['releases'];
	$genres=$array['genres'];