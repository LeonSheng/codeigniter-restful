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
}
