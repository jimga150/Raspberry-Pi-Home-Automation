<?php

class Panel
{

    const cmd_state_0_to_1 = 0;
    const cmd_state_1_to_0 = 1;
    const cmd_read = 2;

    const GPIO_READ_CMD = 0;
    const GPIO_WRITE_CMD = 1;

    const DEFAULT_CMD = "false";
    const DEFAULT_CHANGE_CMDS = array(
        self::cmd_state_0_to_1 => self::DEFAULT_CMD,
        self::cmd_state_1_to_0 => self::DEFAULT_CMD
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

    public static function gpio_cmd($pin, $type = self::GPIO_READ_CMD, $state_change = self::cmd_state_0_to_1, $pulse = false, $polarity = 1, $pulse_length_s = 1)
    {
        if ($type == self::GPIO_READ_CMD) {
            return "gpio mode " . $pin . " in; gpio read " . $pin;
        }
        if (!$pulse) {
            $pin_state = $state_change == self::cmd_state_0_to_1 ? "1" : "0";
            return "gpio mode " . $pin . " out; gpio write " . $pin . " " . $pin_state;
        }
        $active = strval($polarity);
        $inactive = $polarity == 1 ? "0" : "1";
        return "gpio mode " . $pin . " out; " .
            "gpio write " . $pin . " " . $inactive . "; " .
            "sleep 0.01; " .
            "gpio write " . $pin . " " . $active . "; " .
            "sleep " . strval($pulse_length_s) . "; " .
            "gpio write " . $pin . " " . $inactive;
    }
}

$panels = array(
    Panel::makePanelChangeOnly(
        "Bedroom Light",
        array(
            Panel::gpio_cmd(1, Panel::GPIO_WRITE_CMD, Panel::cmd_state_0_to_1, true, 0),
            Panel::gpio_cmd(2, Panel::GPIO_WRITE_CMD, Panel::cmd_state_1_to_0, true, 0),
        )
    ),
    Panel::makePanelReadOnly("Front door", "echo 0", "Open", "Closed"),
    Panel::makePanelChangeOnly(
        "Living Room Lights",
        array(
            Panel::gpio_cmd(3, Panel::GPIO_WRITE_CMD, Panel::cmd_state_0_to_1, true, 0),
            Panel::gpio_cmd(4, Panel::GPIO_WRITE_CMD, Panel::cmd_state_1_to_0, true, 0)
        )
    )
);

$num_panels = count($panels);

