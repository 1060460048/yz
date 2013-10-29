<?php

class YzFormatter extends CFormatter {
    public function formatBoolean($value){
        return $value ? Yii::t('Yz.t9n',$this->booleanFormat[1]) : Yii::t('Yz.t9n',$this->booleanFormat[0]);
    }
}