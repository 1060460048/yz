<?php

/**
 * Class YzVarDumper extends Yii's {@see CVarDumper}.
 * It provides ability to export vars in 'comment-style' format to
 * hide output from regular user
 */
class YzVarDumper extends CVarDumper
{
    protected static $_comments = array(
        'php' => array('/* ',' */'),
        'html' => array('<!-- ',' -->'),
        'js' => array('/* ',' */'),
    );
    public static function commentedDump($var, $depth = 10, $highlight = false, $commentStyle = 'html')
    {
        echo self::$_comments[$commentStyle][0] .
            self::dump($var,$depth,$highlight) . self::$_comments[$commentStyle][1];
    }
}