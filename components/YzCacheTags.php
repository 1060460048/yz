<?php

Yii::import('yz.behaviors.YzCacheTagsBehavior');

class YzCacheTags extends CCacheDependency implements ICacheDependency
{
    protected $_timestamp;
    protected $_tags;

    /**
     * @param array|string tag1, tag2, ..., tagN
     */
    function __construct($tag) {
        if(is_array($tag))
            $this->_tags = $tag;
        else
            $this->_tags = func_get_args();
    }

    /**
     * Evaluates the dependency by generating and saving the data related with dependency.
     * This method is invoked by cache before writing data into it.
     */
    public function evaluateDependency()
    {
        $this->_timestamp = time();
    }

    /**
     * @return boolean whether the dependency has changed.
     */
    public function getHasChanged()
    {
        $tags = array_map(create_function('$i','return YzCacheTagsBehavior::PREFIX.$i;'), $this->_tags);
        $values = Yii::app()->cache->mget($tags);

        foreach ($values as $value) {
            if ((integer)$value > $this->_timestamp)
                return true;
        }

        return false;
    }

}