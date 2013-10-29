<?php
/**
 * This class implements possibility of late static binding to classes for
 * PHP 5.2.
 *
 * The main purpose of this class is to allow load classes, located in different
 * paths only on event rise. For PHP5.3+ you can use lambda functions, and this
 * is preferred to speed.
 *
 * The current implementation creates anonymous function for PHP5.2, and lambda function
 * for PHP5.3+. The small advantage of this class for PHP5.3+ is that you don't need
 * to instantiate handler class manually.
 * DEPRECATED. This class was used in php 5.2 version of Yz Events. Currently Yz supports only
 * php 5.3, so this class is no longer supported.
 * @deprecated
 */
class YzEvents extends CComponent
{
    protected $_handlers = array();

    /**
     * @return YzEvents
     */
    public static function getInstance()
    {
        static $instance = null;
        $className = __CLASS__;
        if($instance === null)
            $instance = new $className;
        return $instance;
    }

    /**
     * @param mixed $callback
     * @param array $config
     * @return callable
     */
    public static function getHandler($config, $callback ='handle')
    {
        return self::getInstance()->createHandler($config,$callback);
    }

    /**
     * @param array|string $config
     * @param mixed $callback
     * @return callable
     */
    public function createHandler($config, $callback ='handle')
    {
        if(is_string($config)) {
            $function = create_function('$event',
                '$object = Yii::createComponent(array("class"=>"'.$config.'"));'.
                'return $object->'.$callback.'($event);'
            );
            return $function;
        } else {
            $info = array(
                'callback' => $callback,
                'config' => $config,
            );

            $id = count($this->_handlers)+1;
            $this->_handlers[$id] = $info;
            $function = create_function('$event',"return YzEvents::getInstance()->callHandler({$id},\$event);");
            return $function;
        }
    }

    /**
     * @param int $id
     * @param mixed $arguments
     * @throws CException
     * @return mixed
     */
    public function callHandler($id, $arguments)
    {
        if(!isset($this->_handlers[$id]))
            throw new CException('Unknown handler id');

        $info = $this->_handlers[$id];
        $callback = $info['callback'];

        $object = Yii::createComponent($info['config']);
        if(!is_array($arguments))
            $arguments = array($arguments);
        return call_user_func_array(array($object, $callback), $arguments);
    }
}