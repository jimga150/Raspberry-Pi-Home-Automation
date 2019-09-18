<?php
    $directory = '/var/www/Snapshots_Garage/';
    $files = glob($directory . '*.jpg');

    if ( $files !== false ) {
        $filecount = count( $files );

        $l = $filecount-1;
        $numpics = 0;
        for ($i = $filecount-1; $i >= 0; $i--) {
            $newname = editdate($pictures[$i]);
            if ($i < $filecount-1){
                $oldname = editdate($pictures[$l]);
                $sameminute = (substr($oldname, 0, -2) == substr($newname, 0, -2));
                if (!$sameminute || ($sameminute && intval(substr($oldname, -2)) >= intval(substr($newname, -2))+2)){
                    $numpics++;
                    $l = $i;
                }
            } else {
                $numpics++;
            }
        } 
        echo json_encode($numpics);
    } else {
        echo json_encode(0);
    }

    function editdate($input){
        $input = str_replace("Snapshots_Garage/", "", $input);
        $input = str_replace(".jpg", "", $input);
        if (substr($input, 0, 1) == "t"){
            $input = str_replace("test_", "", $input);
            $input = str_replace("_", "", $input);
        } else if (substr($input, 0, 1) == "g"){
            $input = str_replace("garagecam", "", $input);
        } else if (substr($input, 0, 1) == "p"){
            $input = str_replace("plantcam", "", $input);
        } else {
            echo("<h1>Error!</h1>");
        }
        if (strlen($input) != 14){
            $input = substr($input, 0, 14);
        } 
        if (is_numeric($input)){
            $input = tomonth(substr($input, 4, 2)) . " " . today(substr($input, 6, 2)) . ", " . substr($input, 0, 4) . ", " . tohour(substr($input, 8, 2)) . ":" . substr($input, 10, 2) . ":" . substr($input, 12, 2);
        } else {
            echo("<h1>uh-oh! not numeric title</h1>");
        }
        return $input;
    }

    function tohour($hour){
        $result = intval($hour, 10);

        $daynight = array("A.M", "P.M");
        $suffix = $daynight[(int)($result/12)] . " ";

        if ($result === 0){
            return $suffix . 12;
        } else if ($result > 12){
            return $suffix . ($result-12);
        }
        return $suffix . $result;
    }

    function tomonth($month){
        $result = intval($month, 10);

        switch($result){
        case 0:
            return "month error zero.";
        case 1:
            return "January";
        case 2:
            return "February";
        case 3:
            return "March";
        case 4:
            return "April";
        case 5:
            return "May";
        case 6:
            return "June";
        case 7:
            return "July";
        case 8:
            return "August";
        case 9:
            return "September";
        case 10:
            return "October";
        case 11:
            return "November";
        case 12:
            return "December";
        default:
            return "Month error!!";
        }
    }

    function today($day){
        $result = intval($day, 10);

        $lastdigit = $result % 10;
        $ending = "ending fail";

        switch($lastdigit){
        case 1:
            $ending = "st";
            break;
        case 2:
            $ending = "nd";
            break;
        case 3:
            $ending = "rd";
            break;
        default:
            $ending = "th";
            break;
        }

        return $result . $ending;
    }
?>