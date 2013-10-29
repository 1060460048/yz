<?php

/**
 * Contains methods to work with arrays
 */
class YzArrayHelper
{
    /**
     * @param mixed $key Key of array
     * @param array $array Array to select from
     * @param mixed $default Default value, witch returns when no element found in array
     * @return mixed
     */
    public static function getElementByKey($key, $array, $default = null)
    {
        return isset($array[$key])?$array[$key]:$default;
    }

    /**
     * Return true if $item in the list of elements, false otherwise
     * @param mixed $item
     * @param mixed $elements
     */
    public static function inArray($item, $elements)
    {
        $elements = func_get_args();
        array_shift($elements);

        if(in_array($item, $elements))
            return true;
        else
            return false;
    }

    /**
     * @param array $array
     * @param string $column
     * @return array
     */
    public static function column($array, $column)
    {
        $result = array();
        foreach($array as $key => $value) {
            if(is_array($value) && isset($value[$column])) {
                $result[$key] = $value[$column];
            } elseif(is_object($value)) {
                $result[$key] = $value->$column;
            }
        }
        return $result;
    }
}
