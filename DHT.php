<?php
    $status = array (0, 0, 0, 0);
    # 2>&1 this gives me any error messages in the response as well. 
    # example:
    # exec ("sudo python /var/www/AdafruitDHT.py 11 21 2>&1", $status[0], $return );
    exec ("sudo python /var/www/AdafruitDHT.py 11 21", $status[0], $return );
    if (empty($status[0][0])){
        echo json_encode("python failure: error ".$return);
    } else {
        echo json_encode($status[0][0]); //this line usually succeeds.
    }
?>