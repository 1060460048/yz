<?php

Yii::import('yz.widgets.YzInputWidget');

class YzWisiwigEditorWidget extends YzInputWidget
{
    public $format = 'html';

    public $customSettings = array();

    public function run()
    {
        parent::run();

        $settings = CMap::mergeArray(array(
            'form'=>$this->form,
            'model'=>$this->model,
            'attribute' => $this->attribute,
            'name' => $this->name,
        ), $this->customSettings);

        switch($this->format) {
            case 'html':
                $settings = CMap::mergeArray($settings,array(
                    'language' => Yii::app()->language,
                ));
                $this->widget('yz.widgets.YzCkEditorWidget',$settings);
                break;
            case 'markdown':
            case 'bbcode':
            case 'wiki':
                $settings = CMap::mergeArray($settings,array(
                    'settings' => $this->format,
                ));
                $this->widget('yz.widgets.YzMarkitupWidget',$settings);
                break;
        }
    }
}