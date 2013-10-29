<?php

/**
 * Allows to collect url rules from different Yz Modules
 */
class YzUrlRulesCollectorHelper
{
    /**
     * @static
     */
    static function collect()
    {
        $cache = Yii::app()->cache;
        $cacheId = __CLASS__ . '_urlRules';

        $rules = $cache->get($cacheId);

        if( $rules == false ) {
            $rules = array(
                'prepend' => array(),
                'append' => array(),
            );
            $modules = Yz::get()->getModules();

            foreach($modules as $module) {
                /** @var $module YzWebModule */
                $moduleRules = $module->urlRules;
                if(!empty($moduleRules))
                    $rules = CMap::mergeArray($rules, $moduleRules);
            }
            $cache->set($cacheId, $rules);
        }

        Yii::app()->urlManager->addRules($rules['prepend'], false);
        Yii::app()->urlManager->addRules($rules['append']);
    }
}