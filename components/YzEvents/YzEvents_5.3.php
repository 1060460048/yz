<?php

/**
 * This is implementation of YzEvents for PHP5.3+.
 * It creates lambda function, that calls the handler
 */
class YzEvents extends CComponent
{
    /**
     * @param array|string $config
     * @param string $handler
     * @return callable
     */
    public static function getHandler($config, $handler='handle')
    {
        return function($event) use ($config, $handler) {
            if(is_string($config))
                $config = array('class' => $config);
            $object = Yii::createComponent($config);
            return call_user_func(array($object,$handler),$event);
        };
    }
}