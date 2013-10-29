<?php

class Yz
{
    protected static $_messageId = 'Yz.message';

    /**
     * Shortcut for Yii::app()->getBaseUrl
     * @static
     * @param bool $absolute
     * @return string
     */
    public static function getBaseUrl($absolute = false)
    {
        return Yii::app()->getBaseUrl($absolute);
    }

    /**
     * Shortcut for {@link CHttpRequest::getBaseUrl}
     * @static
     * @return string
     */
    public static function getRequestBaseUrl()
    {
        return Yii::app()->request->baseUrl;
    }

    /**
     * This function is a shortcut for Yii::app()->yz.
     * For more details see {@link YzComponent}
     * @static
     * @return YzComponent
     */
    public static function get()
    {
        return Yii::app()->yz;
    }

    /**
     * @param $message
     * @param string $header
     * @param string $type
     * @param string $messageId
     */
    public static function message($message, $header = null, $type = 'success', $messageId = null)
    {
        $messageId = ($messageId==null)?self::$_messageId:$messageId;
        /** @var $user CWebUser */
        $user = Yii::app()->user;
        $user->setState($messageId, array(
            'message' => $message,
            'header' => $header,
            'type' => $type,
        ));

        Yii::app()->request->redirect(CHtml::normalizeUrl(array('/yzSystem/default/message')));
    }

    /**
     * @param string $messageId
     * @return string
     */
    public static function getMessage($messageId = null)
    {
        $messageId = ($messageId==null)?self::$_messageId:$messageId;
        /** @var $user CWebUser */
        $user = Yii::app()->user;
        $message = $user->getState($messageId);
        $user->setState($messageId,null);

        return $message;
    }

    /**
     * Returns cache component of application with name $cacheName.
     * If $cacheName set to null, it will return instance of CDummyCache()
     *
     * @static
     * @param $cacheName string
     * @return CCache
     */
    public static function cache($cacheName = 'cache')
    {
        static $dummyCache = null;

        if($cacheName === null)
            return ($dummyCache === null)? ($dummyCache = new CDummyCache()) : $dummyCache;
        else
            return Yii::app()->{$cacheName};
    }

    /**
     * Clears all cache components of the application, that listed in the
     * {@see YzComponent::$cacheComponents} property. If $tags parameter
     * is not empty, than only cache values with tags from $tags array
     * will be deleted. To use $tags parameter of this function, all
     * cache components of application should have {@see YzCacheTagsBehavior} behavior.
     * See also {@see YzCacheTagsBehavior} and {@see YzCacheTags}
     *
     * @param array $tags List of tags that should be deleted
     */
    public static function clearCache($tags = array())
    {
        if(!is_array(self::get()->cacheComponents))
            $cacheComponents = array('cache');
        else
            $cacheComponents = self::get()->cacheComponents;

        if($tags == array())
            foreach($cacheComponents as $cacheName)
                Yz::cache($cacheName)->flush();
        else
            foreach($cacheComponents as $cacheName)
                Yz::cache($cacheName)->clear($tags);
    }

    /**
     * Shortcut for {@see YzComponent::$extensionsLinker}'s method
     * {@see YzExtensionsLinker::getExtensionPath}
     * @param string $name Extension name
     * @param string $default Default path to extension
     * @return string
     */
    public static function getExtensionPath($name, $default = null)
    {
        return Yz::get()->getExtensionsLinker()->getExtensionPath($name,$default);
    }

    /**
     * Shortcut for {@see getExtensionPath}
     * @param string$name
     * @param string $default
     * @return string
     */
    public static function gep($name, $default = null)
    {
        return self::getExtensionPath($name,$default);
    }

    /**
     * Checks if we currently working in {@link YiiComponent::$developerMode developer mode}
     * @static
     * @param bool $throwError If true function will throw CHttpException(400), otherwise it will return boolean value
     * @return bool true if we in developer mode
     * @throws CHttpException If $throwError is set to true
     */
    public static function isDeveloperMode($throwError = true)
    {
        if(self::get()->developerMode == false) {
            if($throwError)
                throw new CHttpException(400);
            else
                return false;
        } else
            return true;
    }

    /**
     * Checks whether we in admin panel (backend) or not
     */
    public static function isInBackend()
    {
        $c = Yii::app()->getController();

        if($c === null || !($c instanceof YzBackController))
            return false;
        else
            return true;
    }

    /**
     * @return CWebUser|YzUsersWebUser
     */
    public static function user()
    {
        return Yii::app()->user;
    }

    /**
     * Returns Yz Extension version
     * @return string
     */
    public static function getVersion()
    {
        return '0.1';
    }
}