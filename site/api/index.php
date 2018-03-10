<?php
    $result = DbApi.getTableData();

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
        //moved to tid-table-template.html
    }
?>
<!-- //dnb//(<?php echo count($dnb); ?>, +<?php echo $recent_dnb; ?>) -->
<!-- //dubstep//(<?php echo count($dubstep); ?>, +<?php echo $recent_dubstep; ?>) -->

<div class="tid-table tid-table-dnb"
   tid-table="dnb">
</div>
<div class="tid-table tid-table-dubstep"
   tid-table="dubstep">
</div>

<?php die(); ?>
