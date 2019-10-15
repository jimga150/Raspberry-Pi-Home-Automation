<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <title>Raspberry Pi Home Automation</title>

    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
    <link rel="stylesheet" type="text/css" href="stylesheets/fancythings.css">
    <meta name="mobile-web-app-capable" content="yes"/>
    <meta name="theme-color" content="#3f3f3f"/>
</head>
<body width="1280" ontouchstart=""> <!--https://stackoverflow.com/questions/27955077/html-mobile-buttons-display-->
<?php
include "panels.php";

// Define your username and password
$username = "Landing St";
$password = "EU@F*%Rz4D'q{6tR";
$logged_in = false;

if ($_POST['txtUsername'] != $username || $_POST['txtPassword'] != $password) {
    ?>
    <h1>Login</h1>
    <form name="form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <p>
            <label for="txtUsername">Username:</label>
            <br>
            <input type="text" title="Enter your Username" name="txtUsername"/>
        </p>
        <p>
            <label for="txtpassword">Password:</label>
            <br>
            <input type="password" title="Enter your password" name="txtPassword"/>
        </p>
        <p><input type="submit" name="Submit" value="Login"/></p>
    </form>
    <?php
} else {//here begins the page loaded after the login is achieved
    $logged_in = true;
    $return = 0;
    $output = "";
    echo("<div><table>");
    for ($i = 0; $i < $num_panels; $i++) {

        echo("<tr>");
        echo("<td class='Label' style='text-align: right'>" . $panels[$i]->name . "</td>");


        if ($panels[$i]->changeable == 1) {
            echo("<td><a class='glowBtn Green' id='panel_" . $i . "_on'>On</a></td>");
            echo("<td><a class='glowBtn Red' id='panel_" . $i . "_off'>Off</a></td>");
        }

        if ($panels[$i]->readable == 1) {
            exec($panels[$i]->read_command, $output, $return);
            //echo("<td>".$output[0]." ".$return."</td>");
            $panels[$i]->state = intval($output[0]);
            $status_text = $panels[$i]->status_texts[$panels[$i]->state];
            echo("<td id='status_" . $i . "' class='status status_" . $panels[$i]->state . "'>" . $status_text . "</td>");
        }

        echo("</tr>");
    }
    echo("</table></div>");
}
//phpinfo();
?>

<script>
    <?php
    if ($logged_in){
    for ($i = 0; $i < $num_panels; $i++) {
        if ($panels[$i]->changeable == 1) {
            echo("let on_button_" . $i . " = document.getElementById(\"panel_" . $i . "_on\");\n\t");
            echo("let off_button_" . $i . " = document.getElementById(\"panel_" . $i . "_off\");\n\t");
            echo("on_button_" . $i . ".addEventListener(\"click\", send_cmd.bind(null, \"" . $i . "\", \"" . Panel::cmd_state_0_to_1 . "\"));\n\t");
            echo("off_button_" . $i . ".addEventListener(\"click\", send_cmd.bind(null, \"" . $i . "\", \"" . Panel::cmd_state_1_to_0 . "\"));\n\t");
        }
        if ($panels[$i]->readable) {
            echo("let status_text_" . $i . " = document.getElementById(\"status_" . $i . "\");");
            //TODO: add interval to send read requests
        }
    }
    ?>
    function send_cmd(panel_id, cmd_type) {
        let request = new XMLHttpRequest();
        request.open("POST", "run_command.php", true);
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.send("panel_id=" + panel_id + "&cmd_type=" + cmd_type);
        request.onreadystatechange = function () {
            if (request.readyState === 4 && request.status === 200) {
                if (request.responseText.includes("Fail")) {
                    console.log(request.responseText);
                } else {
                    if (request.responseText === "[]"){
                        console.log("Command Success, no output");
                    } else {
                        console.log(request.responseText);
                    }

                }
            } else if (request.readyState === 4 && request.status === 500) {
                console.log("Fail - Server Error");
            }
        };
    }
    <?php
    }
    ?>
</script>

</body>
</html>