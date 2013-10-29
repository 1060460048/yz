<?php

class YzDateTimeBehavior extends CActiveRecordBehavior
{
    /**
     * @var array Attributes array in format db_attribute => YzDateTime_attribute
     */
    public $attributes = array();

    protected function beforeValidate($event)
    {
        $this->assignDbAttributes();
    }

    protected function beforeSave($event)
    {
        $this->assignDbAttributes();
    }

    protected function afterFind()
    {
        $this->assignDatetimeAttributes();
    }


    protected function assignDatetimeAttributes()
    {
        foreach($this->attributes as $dbAttribute => $datetimeAttribute) {
            $this->owner->{$datetimeAttribute}->mysql = $this->owner->{$dbAttribute};
        }
    }

    protected function assignDbAttributes()
    {
        foreach($this->attributes as $dbAttribute => $datetimeAttribute) {
            $this->owner->{$dbAttribute} = $this->owner->{$datetimeAttribute}->mysql;
        }
    }
}