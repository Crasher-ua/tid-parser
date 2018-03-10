<?php
    echo '<!-- real load -->';
    //$releases,$genres
    include_once('lib/ganon.php');
    include_once('config.php');

    if($errors){
        error_reporting(E_ALL);
        ini_set('display_errors',1);
    }

    function gzdecode($data){
        return gzinflate(substr($data,10,-8)); 
    }

    ini_set('memory_limit','500M');

    $text_encoded=file_get_contents($url);
    $text_xml=gzdecode($text_encoded);
    $text_xml=str_replace("image:","image",$text_xml);
    $array=simplexml_load_string($text_xml);
    $json=json_encode($array);
    $array=json_decode($json,TRUE);
    $releases_all=$array['url'];

    function clear_name($str){
        $str=str_replace("-"," ",$str);
        $str=preg_replace("/(l|e)p$/","$1P",$str);
        $str=ucwords($str);//ucfirst
        return $str;
    }

    function string2data($str){
        $track_pattern="/\/\/www\.trackitdown\.net\/track\/([\w\-]+)\/([\w\-]+)\/([\w\-]+)\/(\d+)\.html/";
        if(preg_match($track_pattern,$str,$matches)){
            $track=$matches[2];
            $track=str_replace("-original-mix","",$track);
            $track=clear_name($track);

            $author=clear_name($matches[1]);

            return array(
                'author'    =>$author,
                'track'     =>$track,
                'genre'     =>$matches[3],
                'id'        =>$matches[4]
            );
        }else{
            return null;
        }
    }

    $releases=array();

    $genres_keys=array();
    foreach($releases_all as $release){
        $data=string2data($release['loc']);
        if($data){
            $genres_keys[$data['genre']]=1;
            if(in_array($data['genre'],$our_genres)){
                $data['img']=$release['imageimage']['imageloc'];
                $data['date']=$release['lastmod'];
                $releases[]=$data;
            }
        }else{
            $genres_keys['error']=1;
        }
    }
    $genres=array();
    foreach($genres_keys as $genre=>$true){
        $genres[]=$genre;
    }

    if(isset($have_to_cache) && $have_to_cache){
        $data=array(
            'releases'=>$releases,
            'genres'=>$genres
        );
        file_put_contents($cache_url,json_encode($data,JSON_FORCE_OBJECT));
    }
