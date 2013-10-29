<?php

/**
 * @method string getAssetsUrl() Publishes and returns url of assets
 */
class YzWidget extends CWidget
{
    function __construct()
    {
        parent::__construct();

        $this->attachBehavior('assets', array(
            'class' => 'yz.behaviors.YzAssetsBehavior',
            'assetsPath' => '{classDir}/assets/{className}',
        ));
    }

}