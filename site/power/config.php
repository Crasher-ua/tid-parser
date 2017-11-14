<?php
	$our_genres=array('drum_and_bass','dubstep','breaks','house');
	$our_genres_tid=array('Drum & Bass','Dubstep','Breaks','House / Electro');
	//$url='tracks_1.xml.gz';
	$url='https://www.trackitdown.net/tracks_1.xml.gz';
	$cache_url='releases_cached.json';
	$errors=true;
	$cache=true;
	$limit_days=3;

	$lets_play_database=true;
	$hostname='localhost';
	$username='';
	$password='';
	$database='';
	include_once('db-config.php');
