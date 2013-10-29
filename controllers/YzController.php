<?php

/**
 * @property-read string $cacheId Cache identifier of this controller
 */
class YzController extends CController
{
    public function t($category,$message,$params=array(),$source=null,$language=null)
    {
        if(($module=$this->getModule())!==null) {
            $component = get_class($module);
            $category = $component.'.'.$category;
        }
        return Yii::t($category,$message,$params,$source,$language);
    }

    public function getCacheId()
    {
        $cacheId = '';
        if(($module = $this->getModule())!==null)
            $cacheId .= $module->getCacheId();
        return  $cacheId.'.'.$this->getId();
    }

    public function getAssetsUrl($path = null)
    {
        if($path === null) {
            if($this->module !== null) {
                if($this->module instanceof YzWebModule)
                    return $this->module->getAssetsUrl($path);
                else
                    $path = 'application.modules.'.$this->module->id.'.assets';
            } else
                $path = 'application.assets';
        }
        $assetsPath = Yii::getPathOfAlias($path);
        // TODO Move this (false,-1,YII_DEBUG) to asset manager configuration
        $assetsUrl = Yii::app()->assetManager->publish($assetsPath, false, -1, Yz::get()->alwaysUpdateAssets);
        return $assetsUrl;
    }

    /**
     * Provides easy access to {@see CClientScript::registerScript} method to include
     * raw JavaScript code into your views
     */
    public function beginScript($id, $position = CClientScript::POS_READY, $stripScriptTag = true)
    {
        array_push($this->_scriptsStack, array(
            'id' => $id,
            'position' => $position,
            'stripScriptTag' => $stripScriptTag,
        ));
        ob_start();
    }

    /**
     * @throws CException
     */
    public function endScript()
    {
        $config = array_pop($this->_scriptsStack);
        if(is_null($config)) {
            throw new CException(Yii::t('FilesModule.t9n','{controller} has an extra endScript() call in its view.',
                array('{controller}'=>get_class($this))));
        }
        $script = ob_get_clean();
        if($config['stripScriptTag'] == true) {
            $script = preg_replace('#^[\r\n\s]*<script\s+[^>]*>#ms','',$script);
            $script = preg_replace('#</script\s*[^>]*>[\r\n\s]*$#ms','',$script);
        }
        /** @var $cs CClientScript */
        $cs = Yii::app()->clientScript;
        $cs->registerScript($config['id'],$script,$config['position']);
    }
}