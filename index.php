<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <title>Raspberry Pi Home Automation</title>

    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/>
    <link rel="stylesheet" type="text/css" href="stylesheets/fancythings.css">
    <meta name="mobile-web-app-capable" content="yes"/>
    <meta name="theme-color" content="#F00BA4"/>
</head>
<body width="1280" ontouchstart=""> <!--https://stackoverflow.com/questions/27955077/html-mobile-buttons-display-->
<?php

class Panel
{

    const state_0_to_1 = 0;
    const state_1_to_0 = 1;

    const GPIO_READ_CMD = 0;
    const GPIO_WRITE_CMD = 1;

    const DEFAULT_CMD = "false";
    const DEFAULT_CHANGE_CMDS = array(
        self::state_0_to_1 => self::DEFAULT_CMD,
        self::state_1_to_0 => self::DEFAULT_CMD
    );

    const DEFAULT_STATUS_TEXTS = array(
        0 => "Off",
        1 => "On"
    );

    public $name = "Panel";

    public $readable = false;
    public $read_command = self::DEFAULT_CMD;

    public $changeable = false;
    public $state_change_cmds = self::DEFAULT_CHANGE_CMDS; //only used when $changeable is true


    public $state = 0;
    public $status_text_0 = "Off";
    public $status_text_1 = "On";
    public $status_texts = self::DEFAULT_STATUS_TEXTS;

    private function __construct($name, $readable, $read_command, $status_text_0, $status_text_1, $changeable = false, $state_change_cmds = self::DEFAULT_CHANGE_CMDS)
    {
        $this->name = $name;

        $this->readable = $readable;
        $this->read_command = $read_command;

        $this->changeable = $changeable;
        $this->state_change_cmds = $state_change_cmds;

        $this->status_texts[0] = $status_text_0;
        $this->status_texts[1] = $status_text_1;
    }

    public static function makePanelReadOnly($name, $read_command, $status_text_0 = "Off", $status_text_1 = "On")
    {
        return new panel($name, true, $read_command, $status_text_0, $status_text_1);
    }

    public static function makePanelChangeOnly($name, $state_change_cmds)
    {
        return new panel($name, false, "", "", "", true, $state_change_cmds);
    }

    public static function makePanelBoth($name, $read_command, $status_text_0 = "Off", $status_text_1 = "On", $state_change_cmds = self::DEFAULT_CHANGE_CMDS)
    {
        return new panel($name, true, $read_command, $status_text_0, $status_text_1, true, $state_change_cmds);
    }

    public static function gpio_cmd($pin, $type = self::GPIO_READ_CMD, $state_change = self::state_0_to_1, $pulse = false, $polarity = 1, $pulse_length_s = 1)
    {
        if ($type == self::GPIO_READ_CMD) {
            return "gpio mode " . $pin . " in; gpio read " . $pin;
        }
        if (!$pulse) {
            $pin_state = $state_change == self::state_0_to_1 ? "1" : "0";
            return "gpio mode " . $pin . " out; gpio write " . $pin . " " . $pin_state;
        }
        $active = strval($polarity);
        $inactive = $polarity == 1 ? "0" : "1";
        return "gpio mode " . $pin . " out; " .
            "gpio write " . $pin . " " . $inactive . "; " .
            "sleep " . strval($pulse_length_s) . "; " .
            "gpio write " . $pin . " " . $active . "; " .
            "sleep " . strval($pulse_length_s) . "; " .
            "gpio write " . $pin . " " . $inactive;
    }
}

$panels = array(
    Panel::makePanelChangeOnly(
        "Bedroom Light",
        array(
            Panel::gpio_cmd(1, Panel::GPIO_WRITE_CMD, Panel::state_0_to_1, true, 0),
            Panel::gpio_cmd(2, Panel::GPIO_WRITE_CMD, Panel::state_1_to_0, true, 0),
        )
    ),
    Panel::makePanelReadOnly("Front door", "echo 0", "Open", "Closed"),
    Panel::makePanelChangeOnly(
        "Living Room Light",
        array(
            Panel::gpio_cmd(3, Panel::GPIO_WRITE_CMD, Panel::state_0_to_1, true, 0),
            Panel::gpio_cmd(4, Panel::GPIO_WRITE_CMD, Panel::state_1_to_0, true, 0)
        )
    ),

);

$num_panels = count($panels);

// Define your username and password
$username = "Jim Landing St";
$password = "$=:6;Cl.a<%C>'l";

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

    // noinspection JSAnnotator
    //const num_panels = <?=$num_panels?>;

    <?php

    for ($i = 0; $i < $num_panels; $i++) {
        if ($panels[$i]->changeable == 1) {
            echo("let on_button_" . $i . " = document.getElementById(\"panel_" . $i . "_on\");\n\t");
            echo("let off_button_" . $i . " = document.getElementById(\"panel_" . $i . "_off\");\n\t");
            echo("on_button_" . $i . ".addEventListener(\"click\", send_cmd.bind(null, \"".$panels[$i]->state_change_cmds[Panel::state_0_to_1]."\"));\n\t");
            echo("off_button_" . $i . ".addEventListener(\"click\", send_cmd.bind(null, \"".$panels[$i]->state_change_cmds[Panel::state_1_to_0]."\"));\n\t");
        }
        if ($panels[$i]->readable) {
            echo("let status_text_" . $i . " = document.getElementById(\"status_" . $i . "\");");
            //TODO: add interval to send read requests
        }
    }
    ?>
    function send_cmd(command) {
        let request = new XMLHttpRequest();
        request.open("POST", "run_command.php", true);
        request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        request.send("cmd=" + command);
        request.onreadystatechange = function () {
            if (request.readyState === 4 && request.status === 200) {
                if (request.responseText.includes("Fail")) {
                    console.log(request.responseText);
                } else {
                    console.log(request.responseText);
                }
            } else if (request.readyState === 4 && request.status === 500) {
                console.log("Fail - Server Error");
            }
        };
    }
</script>

</body>
</html>