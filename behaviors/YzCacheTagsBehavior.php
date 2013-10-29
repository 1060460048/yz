<?php

/**
 * Tags behavior for all cache classes
 * @link http://korzh.net/2011-04-tegirovanie-kesha-v-yii-framework-eto-ne-bolno.html
 */
class YzCacheTagsBehavior extends CBehavior
{
    const PREFIX = '__tag__';

    /**
     * Инвалидирует данные, помеченные тегом(ами)
     *
     * @param array $tags
     * @return void
     */
    public function clear($tags) {

        /** @var $owner CCache */
        $owner = $this->owner;

        foreach ((array)$tags as $tag) {
            $owner->set(self::PREFIX.$tag, time());
        }
    }
}