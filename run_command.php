<?php

$lock_filename = "picontrol.lock";

$tries = 0;
$timeout = false;
if(file_exists($lock_filename)){
    echo json_encode("Failure - command still in progress");
    exit();
}

$lock_file_handle = NULL;
if (!$lock_file_handle = fopen($lock_filename, "w+")){
    echo json_encode("Failure - can't create lock file");
    exit();
}

if (!isset($_POST["cmd"])) {
    echo json_encode("Failure - no command provided");
    exit();
}

$cmd = strip_tags($_POST["cmd"]);

exec($cmd, $output, $return);

$response = "Failure";
if ($return != 0){
    $response = "Failure - command exited with non-zero status. Output: ".$output;
} else {
    $response = $output;
}

if (!fclose($lock_file_handle)){
    echo json_encode("Failure - failed to close lock file");
    exit();
}

if (!unlink($lock_filename)){
    echo json_encode("Failure - failed to delete lock file");
    exit();
}

echo json_encode($response);

