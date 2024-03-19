<?php

namespace App\Helper;

class FlashBag
{

    public static function has($name)
    {
        return isset($_SESSION['flashbag'][$name]);
    }

    public static function set($name, $value, $type = 1)
    {
        $value['title'] = 'Admin message';
        $_SESSION['flashbag'][$name] = $value;
        $_SESSION['flashbagType'] = $type;
    }

    public static function get($name, $default = false)
    {
        $ret = (self::has($name) ? $_SESSION['flashbag'][$name] : $default);
        unset($_SESSION['flashbag'][$name]);
        unset($_SESSION['flashbagType']);
        return $ret;
    }

    public static function getType()
    {
        return $_SESSION['flashbagType'];
    }

    public static function getInstance()
    {
        return new self;
    }
}
