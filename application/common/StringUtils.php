<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class StringUtils
{
    const EMPTY = '';

    /**
     * @param string|null $str
     * @return bool
     */
    public static function isBlank($str): bool
    {
        return $str === null || trim($str) === '';
    }

    /**
     * @param string|null $str
     * @return bool
     */
    public static function isNotBlank($str): bool
    {
        return !self::isBlank($str);
    }

    /**
     * @param $str
     * @param $search
     * @return bool
     */
    public static function startWith($str, $search)
    {
        return strpos($str, $search) === 0;
    }

    /**
     * @param $str
     * @param $search
     * @return bool
     */
    public static function endWith($str, $search)
    {
        $length = strlen($search);
        if($length == 0) {
            return true;
        }
        return (substr($str, -$length) === $search);
    }
}
