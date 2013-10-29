<?php

/**
 * @property array $cacheTags
 */
class YzCacheModelBehavior extends CActiveRecordBehavior
{
    /**
     * @var string|bool Name of the cache component that will be
     * cleared after change of model. If this property is set to false
     * than {@see Yz::clearCache()} method will be called (witch means
     * that all cache components defined in {@see YzComponent::$cacheComponents}
     * will be cleared. Default is 'false'
     */
    public $clearCacheName = false;
    /**
     * @var int the number of seconds that query results may remain valid in cache.
     * If this is 0, the caching will be disabled.
     */
    public $duration = 1000;
    /**
     * @var bool Enable caching or not
     */
    public $cacheEnabled = true;

    protected $_cacheTags = null;

    public function afterSave($event)
    {
        if($this->cacheEnabled)
            $this->clearCache();
    }

    public function afterDelete($event)
    {
        if($this->cacheEnabled)
            $this->clearCache();
    }

    /**
     * @param string|array $cacheTags
     */
    public function setCacheTags($cacheTags)
    {
        if(!is_array($cacheTags))
            $cacheTags = array($cacheTags);
        $this->_cacheTags = $cacheTags;
    }

    /**
     * @return array
     */
    public function getCacheTags()
    {
        if($this->_cacheTags === null)
            $this->_cacheTags = array(get_class($this->owner));
        return $this->_cacheTags;
    }

    protected function clearCache()
    {
        if($this->clearCacheName === false)
            Yz::clearCache();
        else
            Yz::cache($this->clearCacheName)->clear($this->cacheTags);
    }
}