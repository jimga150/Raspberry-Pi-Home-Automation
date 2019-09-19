<?php
include "panels.php";

$lock_filename = "picontrol.lock";

$tries = 0;
$timeout = false;
if (file_exists($lock_filename)) {
    echo json_encode("Failure - command still in progress");
    exit();
}

$lock_file_handle = NULL;
if (!$lock_file_handle = fopen($lock_filename, "w+")) {
    echo json_encode("Failure - can't create lock file");
    exit();
}

if (!isset($_POST["panel_id"])) {
    echo json_encode("Failure - no panel ID provided");
    exit();
}
$panel_index = strip_tags($_POST["panel_id"]);

if (!isset($_POST["cmd_type"])) {
    echo json_encode("Failure - no command type provided");
    exit();
}
$cmd_type = strip_tags($_POST["cmd_type"]);

$cmd = "false";
if ($cmd_type == Panel::cmd_read) {
    $cmd = $panels[$panel_index]->read_command;
} else {
    $cmd = $panels[$panel_index]->state_change_cmds[$cmd_type];
}

exec($cmd." 2>&1", $output, $return);

$response = "Failure";
if ($return != 0) {
    $response = "Failure - command exited with non-zero status. Output (".count($output)." lines): ";
    for ($i = 0; $i < count($output); $i++){
        $response .= $output[$i];
    }
} else {
    $response = $output;
}

if (!fclose($lock_file_handle)) {
    echo json_encode("Failure - failed to close lock file");
    exit();
}

if (!unlink($lock_filename)) {
    echo json_encode("Failure - failed to delete lock file");
    exit();
}

echo json_encode($response);

