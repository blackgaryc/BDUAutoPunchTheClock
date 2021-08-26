<?php

class RandString
{
    /**
     * @var string
     */
    private static $chars = "0123456789zxcvbnmasdfghjklqwertyuiopZXCVBNMASDFGHJKLQWERTYUIOP";
    static int $count = 0;

    private function __construct()
    {
    }

    public static function randString(int $len)
    {
        $str = "";
        while (strlen($str) < $len) {
            $str .= self::$chars[rand(0, strlen(self::$chars) - 1)];
        }
        return $str;
    }
}
