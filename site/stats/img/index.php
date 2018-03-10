<?php
    ini_set('memory_limit','600M');//513-600

    //$connection=mysql_connect($hostname,$username,$password,$database);
    //if(!$connection)
        //die('error: failed to connect to MySQL: '.mysql_error());
    //mysql_select_db($database,$connection);
    //mysql_set_charset('utf8',$connection);

    $height=1;
    $marker_margin_left=10;
    $marker_margin_right=10;
    $marker_width=10;
    $marker_height=10;
    $text_offset_x=30;
    $text_offset_y=7;

    include('../data-tid-genre.php');
    $start=key($tid);
    end($tid);
    $end=key($tid);

    $my_img = imagecreate( 1000+$marker_margin_left+$marker_width+$marker_margin_right+$text_offset_x, ceil(($end-$start)/1000)*$height );

    $background = imagecolorallocate( $my_img, 0, 0, 0 );

    $dnb        = imagecolorallocate($my_img,0,255,0);
    $dubstep    = imagecolorallocate($my_img,255,255,0);
    $other      = imagecolorallocate($my_img,191,191,191);
    $marker     = imagecolorallocate($my_img,0,255,0);
    $marker_text= imagecolorallocate($my_img,255,255,255);

    $offset_y=0;
    for($i=$start;$i<=$end;$i++){
        if($i%1000==0)
            $offset_y+=$height;

        if($i==8020050 || $i%500000==0){
            $start=1000+$marker_margin_left;
            imagefilledpolygon($my_img,array(
                $start,                 $offset_y,
                $start+$marker_width,   $offset_y-floor($marker_height/2),
                $start+$marker_width,   $offset_y+floor($marker_height/2),
            ),3,$marker);
            imagestring($my_img,4,$start+$marker_width+$marker_margin_right,$offset_y-$text_offset_y,number_format($i/1000000,1),$marker_text);
        }

        $color=(!array_key_exists($i,$tid) ? $background : ($tid[$i]=='Drum & Bass' ? $dnb : ($tid[$i]=='Dubstep' ? $dubstep : $other)));
        imageline($my_img, $i%1000,$offset_y,$i%1000,$offset_y+$height, $color);
    }

    header("Content-type: image/png");
    imagepng($my_img);
    imagecolordeallocate($my_img,$line_color);
    imagecolordeallocate($my_img,$background);
    imagedestroy($my_img);

    die();
