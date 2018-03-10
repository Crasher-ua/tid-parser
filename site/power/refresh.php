<?php
    include_once('config.php');
    if($errors){
        error_reporting(E_ALL);
        ini_set('display_errors',1);
    }

    $last_refresh_file='last_refresh.txt';
    $min_resfresh_time=60;
    $can_refresh=true;
    $current_date=new DateTime();

    if(file_exists($last_refresh_file)){
        $last_refresh=new DateTime(file_get_contents($last_refresh_file));
        $seconds_diff=$current_date->format('U')-$last_refresh->format('U');
        if($seconds_diff<$min_resfresh_time){
            $can_refresh=false;
        }
    }

    if(!$can_refresh){
        echo 'try again later (after '.($min_resfresh_time-$seconds_diff).' seconds)';
    }else{
        file_put_contents($last_refresh_file,$current_date->format('Y-m-d H:i:s'));

        if(!file_exists($cache_url)){
            echo 'cache doesn\'t exists';
        }else if(!unlink($cache_url)){
            echo 'can not delete cache';
        }else{
            echo 'cache removed';
        }
    }
