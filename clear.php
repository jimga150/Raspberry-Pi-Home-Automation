<?php
    exec ("sudo chmod -R 777 /var/www/Snapshots_Garage", $status[0], $return );

/*if (isset($_GET["num"]){
    $num = strip_tags($_GET["num"]);
    if (is_numeric($num)){
        exec ("sudo python /var/www/collectData.py ".$num, $status[0], $return );
        if (empty($status[0][0])){
            echo json_encode("python failure: error ".$return);
        } else {
            echo json_encode($status[0][0]); //this line usually succeeds.
        }
    }
}*/
    if (!is_dir('/var/www/Snapshots_Garage'))
        echo json_encode("not directory");
    else
        $files = glob('/var/www/Snapshots_Garage/*.jpg');
        foreach($files as $file){
            if(is_file($file))
                unlink($file);
        }
        echo json_encode("success");
?>