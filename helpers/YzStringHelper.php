<?php

class YzStringHelper
{
    /**
     * @param string $text
     * @param string $length
     * @param string $ellipses
     * @return string
     */
    public static function trimWords($text, $length, $ellipses = '...')
    {
        //no need to trim, already shorter than trim length
        if (strlen($text) <= $length) {
            return $text;
        }

        //find last space within length
        $last_space = strrpos(substr($text, 0, $length), ' ');
        $trimmed_text = substr($text, 0, $last_space);

        //add ellipses (...)
        if ($ellipses) {
            $trimmed_text .= $ellipses;
        }

        return $trimmed_text;
    }

    /**
     * Make a string's first character uppercase
     * @param string $string
     * @param string $encoding
     * @return string
     */
    public static function mbUcFirst($string, $encoding = 'utf-8')
    {
        return mb_strtoupper(mb_substr($string,0,1,$encoding),$encoding).
            mb_substr($string,1,mb_strlen($string,$encoding),$encoding);
    }
}