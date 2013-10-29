<?php
/**
 * This file contains different functions, that are widely used
 * by Yz engine (or maybe by Yii Framework), but doesn't exist
 * on some servers or in some versions of PHP.
 */

if(function_exists('lcfirst') === false)
{
    /**
     * Make a string's first character lowercase
     *
     * @param string $str
     * @return string the resulting string.
     */
    function lcfirst( $str ) {
        $str[0] = strtolower($str[0]);
        return (string)$str;
    }
}