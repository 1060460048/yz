<?php

/**
 * @todo Complete this class
 */
class YzOrderBehavior extends CActiveRecordBehavior
{
    public $attribute = 'order';

    /**
     * @return CActiveRecord
     */
    public function ordered()
    {
        $criteria = new CDbCriteria();

        $field = $this->owner->tableAlias . '.' . $this->attribute;

        $criteria->order = $this->owner->getDbConnection()->quoteColumnName($field) . ' ASC';

        $this->owner->getDbCriteria()->mergeWith($criteria);

        return $this->owner;
    }

    public function moveUp()
    {

    }

    public function moveDown()
    {

    }

    protected function beforeSave($event)
    {
        if($this->owner->isNewRecord == false)
            return;


    }


}