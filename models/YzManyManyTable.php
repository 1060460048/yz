<?php

/**
 * Class YzManyManyTable implements general model that is used to access
 * junction table in MANY_MANY relations. If junction table does not exist, than it
 * will be created.
 * @property int $from_record_id
 * @property int $to_record_id
 */
class YzManyManyTable extends YzActiveRecord
{
    const FROM_RECORD_ID = 'from_record_id';
    const TO_RECORD_ID = 'to_record_id';

    /**
     * @var CActiveRecord
     */
    public $fromClass;
    /**
     * @var CActiveRecord
     */
    public $toClass;
    /**
     * @var string
     */
    public $prefix = '';

    protected $_many_many_md = null;
    protected static $_instances = array();

    /**
     * @param string $className
     * @return CActiveRecord|void
     * @throws CException
     */
    public static function model($className = __CLASS__)
    {
        throw new CException('You can not use model method with this class');
    }


    /**
     * @param CActiveRecord $fromClass
     * @param CActiveRecord $toClass
     * @param string $prefix
     */
    public function __construct($fromClass, $toClass, $prefix = 'rel_',$scenario='insert')
    {
        $this->fromClass = $fromClass;
        $this->toClass = $toClass;
        $this->prefix = $prefix;

        /**
         * One of the common error is different order of $fromClass and $toClass. So let's
         * check for another definition, and if it's exits, throw an error
         */
        if($toClass->tableName() != $fromClass->tableName()) {
            $otherId = md5($toClass->tableName().'_'.$fromClass->tableName().'_'.$prefix);
            if(isset(self::$_instances[$otherId]))
                throw new CException('Another order of related models is already defined. Please check for this order: '.
                    get_class($fromClass).' and '.get_class($toClass));
        }

        $this->createTable();
        return parent::__construct($scenario);
    }

    /**
     * Returns an instance of YzManyManyTable attached to the junction table between
     * to models. If junction table does not exist, than it will be created.
     * @param CActiveRecord $fromClass
     * @param CActiveRecord $toClass
     * @param string $prefix
     * @return \YzManyManyTable
     */
    public static function factory($fromClass, $toClass, $prefix = 'rel_')
    {
        $id = md5($fromClass->tableName().'_'.$toClass->tableName().'_'.$prefix);
        if(isset(self::$_instances[$id]))
            return self::$_instances[$id];
        else
            return self::$_instances[$id] = new YzManyManyTable($fromClass,$toClass,$prefix);
    }

    /**
     * @return string
     */
    public function tableName()
    {
        return '{{' . $this->prefix . trim($this->fromClass->tableName(),'{}') . '_to_' .
            trim($this->toClass->tableName(),'{}') . '}}';
    }

    /**
     * Returns relation string: '{{table_name}}(from_record_id, to_record_id)'.
     * It can be used in relation definition of model
     * @param bool $reverse Whether to use first model as related.
     * @return string
     */
    public function relationString($reverse = false)
    {
        if($reverse == false)
            $keys =  self::FROM_RECORD_ID . ',' . self::TO_RECORD_ID;
        else
            $keys =  self::TO_RECORD_ID . ',' . self::FROM_RECORD_ID;

        return $this->tableName() . '(' . $keys . ')';
    }

    /**
     * Returns relation array, that can be used in relations() function of model.
     * Using this function you can write following expressions:
     * <code>
     * function relations()
     * {
     *  return array(
     *      'relationName' => YzManyManyTable::factory(ModelOne::model(),ModelTwo::model())
     *          ->relationDefinition(),
     *  );
     * }
     * </code>
     * @param bool $reverse Whether to use first model as related.
     * @return array
     * @see CActiveRecord::relations
     */
    public function relationDefinition($reverse = false)
    {
        if($reverse == false)
            $relatedTo = get_class($this->toClass);
        else
            $relatedTo = get_class($this->fromClass);

        return array(self::MANY_MANY, $relatedTo, $this->relationString($reverse));
    }

    public function getMetaData()
    {
        if($this->_many_many_md!==null)
            return $this->_many_many_md;
        else {
            $this->refreshMetaData();
            return $this->_many_many_md;
        }
    }

    public function refreshMetaData()
    {
        $md=new CActiveRecordMetaData($this);
        if($this->_many_many_md!==$md)
            $this->_many_many_md=$md;
    }

    /**
     * @param array $attributes
     * @return $this
     */
    protected function instantiate($attributes = array())
    {
        $class=get_class($this);
        $model=new $class($this->fromClass,$this->toClass,$this->prefix);
        return $model;
    }


    /**
     * @param CActiveRecord|int $from
     * @param CActiveRecord|int $to
     * @return $this
     */
    public function add($from, $to)
    {
        if($from instanceof CActiveRecord)
            $from = $from->getPrimaryKey();
        if($to instanceof CActiveRecord)
            $to = $to->getPrimaryKey();

        $this->getDbConnection()->createCommand()
            ->insert($this->tableName(),array(
                self::FROM_RECORD_ID => $from,
                self::TO_RECORD_ID => $to,
            ));

        return $this;
    }

    /**
     * @param CActiveRecord|int $from
     * @return $this
     */
    public function deleteFrom($from)
    {
        if($from instanceof CActiveRecord)
            $from = $from->getPrimaryKey();
        $this->getDbConnection()->createCommand()
            ->delete($this->tableName(),self::FROM_RECORD_ID.' = :from',array(
                ':from' => $from,
            ));

        return $this;
    }

    /**
     * @param CActiveRecord|int $to
     * @return $this
     */
    public function deleteTo($to)
    {
        if($to instanceof CActiveRecord)
            $to = $to->getPrimaryKey();
        $this->getDbConnection()->createCommand()
            ->delete($this->tableName(),self::TO_RECORD_ID.' = :to',array(
                ':to' => $to,
            ));

        return $this;
    }

    /**
     * @return string
     */
    function __toString()
    {
        return $this->tableName();
    }

    /**
     * Creates junction table if it does not exist
     * @todo Check this code with other database (Postgres, sqlite, etc.)
     */
    protected function createTable()
    {
        if($this->getDbConnection()->schema->getTable($this->tableName()) === null) {
            $db = $this->getDbConnection();

            $command = $db->createCommand();
            $command->createTable($this->tableName(),array(
                self::FROM_RECORD_ID => 'integer',
                self::TO_RECORD_ID => 'integer',
            ), 'ENGINE=InnoDB DEFAULT CHARSET=utf8');

            $command->createIndex(self::FROM_RECORD_ID,$this->tableName(),self::FROM_RECORD_ID);
            $command->createIndex(self::TO_RECORD_ID,$this->tableName(),self::TO_RECORD_ID);

            $fromPk = $this->fromClass->getDbConnection()->getSchema()->getTable($this->fromClass->tableName())
                ->primaryKey;
            $toPk = $this->toClass->getDbConnection()->getSchema()->getTable($this->toClass->tableName())
                ->primaryKey;

            $fromConstraint = 'fl_'.md5($this->tableName() . '_' . self::FROM_RECORD_ID .
                '_' . $this->fromClass->tableName() . '_' . $fromPk . '_fk');
            $toConstraint = 'fl_'.md5($this->tableName() . '_' . self::TO_RECORD_ID .
                '_' . $this->toClass->tableName() . '_' . $toPk . '_fk');

            $command->addForeignKey($fromConstraint,$this->tableName(),self::FROM_RECORD_ID,
                $this->fromClass->tableName(),$fromPk, 'CASCADE','CASCADE');
            $command->addForeignKey($toConstraint,$this->tableName(),self::TO_RECORD_ID,
                $this->toClass->tableName(),$toPk, 'CASCADE','CASCADE');
        }
    }
}