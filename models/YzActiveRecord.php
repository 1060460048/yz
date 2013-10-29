<?php

/**
 * YzActiveRecord class file.
 */

/**
 * @property WithRelatedBehavior $withRelated {@deprecated}
 */
abstract class YzActiveRecord extends CActiveRecord
{
    public function behaviors()
    {
        return array(
            'withRelated'=>array(
                'class'=>Yz::gep('YiiExt').'.WithRelatedBehavior',
            ),
            'relationBehavior'=>array(
                'class' => Yz::gep('YiiExt').'.active-record-relation-behavior.EActiveRecordRelationBehavior',
            )
        );
    }

    /**
     * @var string $class
     * @return YzActiveRecordIterator
     */
    public static function getRecordsIterator($class = __CLASS__)
    {
        return new YzActiveRecordIterator(new $class);
    }

    /**
     * This method is overwriten so that we can create easily accessed getters and
     * setters for attributes (but they would not conflict with existed getters and setters).
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        $getter = 'getAttr_'.$name;
        if(method_exists($this,$getter))
            return $this->$getter();
        else
            return parent::__get($name);
    }

    /**
     * This method is overwriten so that we can create easily accessed getters and
     * setters for attributes (but they would not conflict with existed getters and setters).
     * @param string $name
     * @param mixed $value
     * @return mixed|void
     */
    public function __set($name, $value)
    {
        $setter='setAttr_'.$name;
        if(method_exists($this,$setter))
            $this->$setter($value);
        else
            parent::__set($name, $value);
    }
}