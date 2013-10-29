<?php

class YzJSONFieldBehavior extends CActiveRecordBehavior
{
    public $attribute = null;

    protected $_fieldValue = null;

    public function beforeSave($event)
    {
        $this->_fieldValue = $this->owner->{$this->attribute};
        $this->owner->{$this->attribute} = CJSON::encode($this->owner->{$this->attribute});
    }

    public function afterSave($event)
    {
        $this->owner->{$this->attribute} = $this->_fieldValue;
        $this->_fieldValue = null;
    }

    public function afterFind($event)
    {
        $this->owner->{$this->attribute} = CJSON::decode($this->owner->{$this->attribute});
        $this->_fieldValue = null;
    }
}