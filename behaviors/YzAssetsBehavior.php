<?php

/**
 * YzAssetsBehavior gives you ability to add 'getAssetsUrl' function
 * to any class (usually widgets and controllers).
 */
class YzAssetsBehavior extends CBehavior
{
    /**
     * @var string Assets dir for this class. Usually this is '{classDir}/assets'.
     * You can also use {className} and {classDir} placeholders
     */
    public $assetsPath = '{classDir}/assets';

    public function getAssetsUrl()
    {
        $ref = new ReflectionClass($this->owner);

        $assetsPath = strtr($this->assetsPath, array(
            '{className}' => get_class($this->owner),
            '{classDir}' => dirname($ref->getFileName()),
        ));

        // TODO Move this (false,-1,YII_DEBUG) to asset manager configuration
        $assetsUrl = Yii::app()->assetManager->publish($assetsPath, false, -1, Yz::get()->alwaysUpdateAssets);
        return $assetsUrl;
    }
}