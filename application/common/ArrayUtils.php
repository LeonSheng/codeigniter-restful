<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ArrayUtils
{
    /**
     * @param array $arr
     * @param string $search
     * @return bool
     */
    public static function anyStartWith(array $arr, string $search): bool
    {
        foreach ($arr as $str) {
            if (StringUtils::startWith($str, $search)) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param string $str
     * @param array $search
     * @return bool
     */
    public static function startWithAny(string $str, array $search): bool
    {
        foreach ($search as $searchStr) {
            if (StringUtils::startWith($str, $searchStr)) {
                return true;
            }
        }
        return false;
    }
}
