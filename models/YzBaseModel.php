<?php

class YzBaseModel extends YzActiveRecord
{
    public function attributesValues()
    {
        return array();
    }

    /**
     * @param string $attribute
     * @param bool|string $keysOnly If string function returns value of specified attribute value (by key)
     * @return array|string
     */
    public function attributeValues($attribute, $keysOnly = false)
    {
        $values = $this->attributesValues();
        if(!isset($values[$attribute]))
            return array();
        if(is_string($keysOnly)) {
            if(isset($values[$attribute][$keysOnly]))
                return $values[$attribute][$keysOnly];
            else
                return null;
        } else
            return $keysOnly ? array_keys($values[$attribute]) : $values[$attribute];
    }

    /**
     * @param string $attribute
     * @param string $dir
     * @return YzBaseModel
     */
    public function orderedBy($attribute, $dir = CSort::SORT_ASC)
    {
        $criteria = new CDbCriteria();
        $criteria->order = "{$this->tableAlias}.`{$attribute}` {$dir}";
        $this->getDbCriteria()->mergeWith($criteria);
        return $this;
    }
}