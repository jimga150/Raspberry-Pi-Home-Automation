<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <title>Raspberry Pi Home Automation</title>
        
        <link rel="shortcut icon" href="http://www.iconj.com/ico/8/h/8hqoc12qgv.ico" type="image/x-icon" />
        <!--grab favicon from external host^v-->
        <!--<link rel="shortcut icon" href="http://www.iconj.com/ico/d/d/dd0nzynxus.ico" type="image/x-icon" />-->
        
        <link rel="stylesheet" type="text/css" href="sass/fancythings.scss">
    </head>
    <body>
        <?php 
    // Define your username and password 
    /*$username = "A2:<}93=<W1 ,ef"; 
    $password = "$=:6;Cl.a<%C>'l"; 

    if ($_POST['txtUsername'] != $username || $_POST['txtPassword'] != $password) { 
    ?> 
    <h1>Login</h1> 
    <form name="form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>"> 
        <p>
            <label for="txtUsername">Username:</label> 
        <br />
            <input type="text" title="Enter your Username" name="txtUsername" />
        </p> 
        <p>
            <label for="txtpassword">Password:</label> 
        <br />
            <input type="password" title="Enter your password" name="txtPassword" />
        </p> 
        <p><input type="submit" name="Submit" value="Login" /></p>
    </form> 
    <?php 
    } 
    else { 
        //here begins the page loaded after the login is achieved
        */
    ?> 
        <button style = "font-size: 100%" onclick="location.reload()">Update pictures</button>
        <button style = "font-size: 100%" onclick="clearpictures()">Clear all pictures</button>
        <button style = "font-size: 100%" onclick="window.location.replace('http://picontrol.ddns.net/index.php');">Back to Main Hub</button>
        <?php
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
            // Get png's from directory 
            $pictures = glob("Snapshots_Garage/*.jpg"); 
            $count = count($pictures);
            usort($pictures, create_function('$a,$b', 'return filemtime($a) - filemtime($b);'));
        
            echo ("<p id = \"title\" style = \"font-size: 250%\">");
            if ($count > 1){
                echo("Here are the snapshots (latest first, ".$count." of them):</p>");
            } else if ($count == 1){
                echo("Here is the latest snapshot:</p>");
            } else if ($count == 0){
                echo("No Snapshots to show ¯\_(ツ)_/¯</p>");
            }

            // Do 4 loops, incrementing $i each time. 
            // Starts from 0 for 0 index in array. 
            echo("<div id = \"picts\">");
            $l = $count-1;
            $numpics = 0;
            for ($i = $count-1; $i >= 0; $i--) {
                $newname = editdate($pictures[$i]);
                if ($i < $count-1){
                    $oldname = editdate($pictures[$l]);
                    $sameminute = (substr($oldname, 0, -2) == substr($newname, 0, -2));
                    if (!$sameminute || ($sameminute && intval(substr($oldname, -2)) >= intval(substr($newname, -2))+2)){
                        $numpics++;
                        echo ("<div>");
                        echo("<p id = \"".$numpics."\">".$newname."</p>");
                        echo("<img src=".$pictures[$i]." alt=\"snapshot ".$numpics."\"/>");
                        echo("<br>");
                        echo("<br>");
                        echo("</div>");
                        $l = $i;
                    }
                } else {
                    $numpics++;
                    echo ("<div><p id = \"".$numpics."\">".$newname."</p><img src=".$pictures[$i]." alt=\"snapshot ".$numpics."\"/>");
                    echo("<br><br></div>"); 
                }
            }
            echo ("</div>");
        
            
        ?>
        <script>
            var x = 1;
            while (document.getElementById(x) !== null){
                  x++; 
            }
            x--;
            if (x == 0){
                document.getElementById("title").innerHTML = ("No Snapshots to show ¯\\_(ツ)_/¯");
            } else if (x == 1){
                document.getElementById("title").innerHTML = ("Here is the latest snapshot:");
            } else {
                document.getElementById("title").innerHTML = ("Here are the snapshots (latest first, "+x+" of them):");
            }
            
            var request = new XMLHttpRequest();
            request.open("GET", "datacollect.php?num=" + x, true);
            request.send();
            request.onreadystatechange = function () {
                if (request.readyState == 4 && request.status == 200) {
                    //document.getElementById("picts").innerHTML = (request.responseText);
                } else if (request.readyState == 4 && request.status == 500) {
                    alert("Server error!!");
                    return ("fail");
                } else {
                    return ("fail");
                }
            };
            function clearpictures(){
                var request = new XMLHttpRequest();
                request.open("GET", "clear.php", true);
                request.send();
                request.onreadystatechange = function () {
                    if (request.readyState == 4 && request.status == 200) {
                        document.getElementById("title").innerHTML = ("No Snapshots to show ¯\\_(ツ)_/¯");
                        document.getElementById("picts").innerHTML = (request.responseText);
                    } else if (request.readyState == 4 && request.status == 500) {
                        alert("Server error!!!");
                        return ("fail");
                    } else {
                        return ("fail");
                    }
                };
            }
        </script>
        <?php 
      /*}*/
    //^here ends the content that is only shown after login. after this bracket, HTML/PHP content will be shown regardless of login.
    //phpinfo();
    ?>
    </body>
</html>