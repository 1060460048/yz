<?php

/**
 * @method string getAssetsUrl() Publishes and returns url of assets
 */
class YzInputWidget extends CInputWidget
{
    /** @var TbActiveForm */
    public $form = null;

    function __construct()
    {
        parent::__construct();

        $this->attachBehavior('assets', array(
            'class' => 'yz.behaviors.YzAssetsBehavior',
            'assetsPath' => '{classDir}/assets/{className}',
        ));
    }

    public function hasForm()
    {
        return !is_null($this->form) && $this->form instanceof TbActiveForm;
    }
}