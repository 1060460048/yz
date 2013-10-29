<?php

class YzActiveRecordIterator implements Iterator
{
    /**
     * @var CActiveRecord
     */
    private $_model;
    /**
     * @var CDbDataReader
     */
    private $_result;
    private $_rowIndex = 0;
    private $_rowCount = 0;

    /**
     * @param $model CActiveRecord
     */
    public function __construct($model) {
        $this->_model = $model;
    }

    /**
     * @param $criteria CDbCriteria
     * @return YzActiveRecordIterator
     */
    public function selectAll($criteria = null) {
        if(is_array($criteria))
            $criteria = new CDbCriteria($criteria);
        if(is_null($criteria))
            $criteria = new CDbCriteria();

        /** @var $command CDbCommand */
        $command = $this->_model->getDbConnection()->getCommandBuilder()
            ->createFindCommand($this->_model->tableName(), $criteria);

        $this->_result = $command->query();
        $this->_rowIndex = 0;
        $this->_rowCount = $this->_result->count();
        return $this; // для MethodChaining
    }

    /**
     * @return CActiveRecord
     */
    public function current () {
        $current = $this->_result->current();
        $model = clone $this->_model; // тут переделаем

        $table=$model->getMetaData()->tableSchema;
        $primaryKey = $table->primaryKey;

        $model->setAttributes($current,false);
        if(!is_null($primaryKey) && !is_array($primaryKey))
            $model->setPrimaryKey($current[$primaryKey]);
        return $model;
    }

    public function next () {
        ++$this->_rowIndex;
        $this->_result->next();
    }

    public function key () {
        return $this->_rowIndex;
    }

    public function valid () {
        return ($this->_rowIndex < $this->_rowCount);
    }

    public function rewind () {
        $this->_rowIndex = 0;
        $this->_result->rewind();
    }
}