<?php
/**
 * YzCrudCode class file.
 * Based on BootstrapCode class by Christoffer Niska
 * @author Pavel Agalecky <pavel.agalecky@gmail.com>
 */

Yii::import('bootstrap.gii.bootstrap.BootstrapCode');

class YzCrudCode extendS BootstrapCode
{
    public $baseControllerClass='YzBackController';

    public $addSearch=false;

    public $t9nCategory='Yz.t9n';

    public function rules()
    {
        return array_merge(parent::rules(), array(
            array('addSearch', 'boolean'),
            array('t9nCategory','sticky'),
        ));
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(),array(
            't9nCategory'=>'Translation Category',
        ));
    }


    /**
     * @param CModel $modelClass
     * @param CDbColumnSchema $column
     * @return string
     */
    public function generateActiveRow($modelClass, $column)
    {
        return parent::generateActiveRow($modelClass, $column);
    }


}
