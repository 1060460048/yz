<?php

/**
 * YzPhpMessageSource differs from yii's {@see CPhpMessageSource} in the way
 * it tries to find message file. The main difference is that this class
 * tries to detect path of the message file, if it belongs to some special
 * parts of your application. This special part currently is "Module".
 * If the category of your message has format NameModule, than it will
 * tries to find message file in messages directory of module with name "Name".
 */
class YzPhpMessageSource extends CPhpMessageSource
{
    const CACHE_KEY_PREFIX='Yz.YzPhpMessageSource.';

    /**
     * @var integer the time in seconds that the messages can remain valid in cache.
     * Defaults to 0, meaning the caching is disabled.
     */
    public $cachingDuration=0;
    /**
     * @var string the ID of the cache application component that is used to cache the messages.
     * Defaults to 'cache' which refers to the primary cache application component.
     * Set this property to false if you want to disable caching the messages.
     */
    public $cacheID='cache';
    /**
     * @var string the base path for all translated messages. Defaults to null, meaning
     * the "messages" subdirectory of the application directory (e.g. "protected/messages").
     */
    public $basePath;

    private $_files=array();

    /**
     * Initializes the application component.
     * This method overrides the parent implementation by preprocessing
     * the user request data.
     */
    public function init()
    {
        parent::init();
        if($this->basePath===null)
            $this->basePath=Yii::getPathOfAlias('application.messages');
    }

    /**
     * Determines the message file name based on the given category and language.
     * If the category name contains a dot, it will be split into the module class name and the category name.
     * In this case, the message file will be assumed to be located within the 'messages' subdirectory of
     * the directory containing the module class file.
     * Otherwise, the message file is assumed to be under the {@link basePath}.
     * @param string $category category name
     * @param string $language language ID
     * @return string the message file path
     */
    protected function getMessageFile($category,$language)
    {
        if(!isset($this->_files[$category][$language]))
        {
            if(($pos=strpos($category,'.'))!==false)
            {
                $moduleClass=substr($category,0,$pos);
                $moduleCategory=substr($category,$pos+1);

                /**
                 * Checking class existence with no autoloading -
                 * as we don't know if this module exists
                 */
                if(!class_exists($moduleClass, false)) {
                    $file = null;
                    if(preg_match('/^(.+)Module$/',$moduleClass,$m)) {
                        $moduleName = strtolower($m[1]);
                        $modules = Yii::app()->getModules();
                        if(isset($modules[$moduleName])) {
                            $file =
                                dirname(Yii::getPathOfAlias($modules[$moduleName]['class'])) .
                                    DIRECTORY_SEPARATOR.'messages'.DIRECTORY_SEPARATOR.
                                    $language.DIRECTORY_SEPARATOR.$moduleCategory.'.php';
                        }
                    }
                    $this->_files[$category][$language] = $file;
                } else {
                    $class=new ReflectionClass($moduleClass);
                    $this->_files[$category][$language]=dirname($class->getFileName()).DIRECTORY_SEPARATOR.'messages'.DIRECTORY_SEPARATOR.$language.DIRECTORY_SEPARATOR.$moduleCategory.'.php';
                }

            }
            else
                $this->_files[$category][$language]=$this->basePath.DIRECTORY_SEPARATOR.$language.DIRECTORY_SEPARATOR.$category.'.php';
        }
        return $this->_files[$category][$language];
    }

    /**
     * Loads the message translation for the specified language and category.
     * @param string $category the message category
     * @param string $language the target language
     * @return array the loaded messages
     */
    protected function loadMessages($category,$language)
    {
        $messageFile=$this->getMessageFile($category,$language);

        if($this->cachingDuration>0 && $this->cacheID!==false && ($cache=Yii::app()->getComponent($this->cacheID))!==null)
        {
            $key=self::CACHE_KEY_PREFIX . $messageFile;
            if(($data=$cache->get($key))!==false)
                return unserialize($data);
        }

        if(is_file($messageFile))
        {
            $messages=include($messageFile);
            if(!is_array($messages))
                $messages=array();
            if(isset($cache))
            {
                $dependency=new CFileCacheDependency($messageFile);
                $cache->set($key,serialize($messages),$this->cachingDuration,$dependency);
            }
            return $messages;
        }
        else
            return array();
    }

}