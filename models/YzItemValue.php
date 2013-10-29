<?php

/**
 * Class YzItemValue implements general model that is used to access
 * junction table in MANY_MANY relations. If junction table does not exist, than it
 * will be created.
 * @property int $record_id
 * @property mixed $value
 */
class YzItemValue extends CComponent
{
    const RECORD_ID = 'record_id';
    const VALUE = 'value';

    /**
     * @var CActiveRecord
     */
    public $class;
    /**
     * @var string
     */
    public $item;
    /**
     * @var string
     */
    public $type;
    /**
     * @var string
     */
    public $prefix = '';

    protected $_item_value_md = null;
    protected static $_instances = array();


    /**
     * @param CActiveRecord $class
     * @param string $item
     * @param string $prefix
     */
    public function __construct($class, $item, $type = 'integer', $prefix = 'item_')
    {
        $this->class = $class;
        $this->item = $item;
        $this->prefix = $prefix;
        $this->type = $type;

        $this->createTable();
    }

    /**
     * @param $class
     * @param $item
     * @param string $type
     * @param string $prefix
     * @return YzItemValueRecordTable
     */
    public static function model($class, $item, $type = 'integer', $prefix = 'item_')
    {
        $class = self::factory($class,$item,$type,$prefix)->relationClass();
        return call_user_func(array($class, 'model'));
    }

    /**
     * Returns an instance of YzManyManyTable attached to the junction table between
     * to models. If junction table does not exist, than it will be created.
     * @param CActiveRecord $class
     * @param string $item
     * @param string $prefix
     * @return \YzItemValue
     */
    public static function factory($class, $item, $type = 'integer', $prefix = 'item_')
    {
        $id = md5($class->tableName().'_'.$item.'_'.$prefix);
        if(isset(self::$_instances[$id]))
            return self::$_instances[$id];
        else
            return self::$_instances[$id] = new YzItemValue($class,$item,$type,$prefix);
    }

    public function tableName()
    {
        return '{{' . $this->prefix . trim($this->class->tableName(),'{}') . '_' .
            $this->item.'}}';
    }

    /**
     * Returns relation string: '{{table_name}}(from_record_id, to_record_id)'.
     * It can be used in relation definition of model
     * @param bool $reverse Whether to use first model as related.
     * @return string
     */
    public function relationString()
    {
        return self::RECORD_ID;
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
     * @param string $type Type of the relation: {@see CActiveRecord::HAS_MANY}
     * or {@see CActiveRecord::HAS_ONE}
     * @throws CException
     * @return array
     * @see CActiveRecord::relations
     */
    public function relationDefinition($type = CActiveRecord::HAS_MANY)
    {
        if(!YzArrayHelper::inArray($type, CActiveRecord::HAS_MANY, CActiveRecord::HAS_ONE))
            throw new CException('Parameter $type cal be either HAS_MANY or HAS_ONE');
        return array($type, $this->relationClass(), $this->relationString());
    }

    /**
     * @param CActiveRecord|int $from
     * @return $this
     */
    public function deleteFromClass($from)
    {
        if($from instanceof CActiveRecord)
            $from = $from->getPrimaryKey();
        $this->class->getDbConnection()->createCommand()
            ->delete($this->tableName(),self::RECORD_ID.' = :from',array(
                ':from' => $from,
            ));

        return $this;
    }

    /**
     * Creates table if it does not exist and also generates class w
     * @todo Check this code with other database (Postgres, sqlite, etc.)
     */
    protected function createTable()
    {
        if($this->class->getDbConnection()->schema->getTable($this->tableName()) === null) {
            $db = $this->class->getDbConnection();

            $command = $db->createCommand();
            $command->createTable($this->tableName(),array(
                'id' => 'int(11) NOT NULL AUTO_INCREMENT',
                self::RECORD_ID => 'integer',
                self::VALUE => $this->type,
                'PRIMARY KEY (`id`)',
            ), 'ENGINE=InnoDB DEFAULT CHARSET=utf8');

            $command->createIndex(self::RECORD_ID,$this->tableName(),self::RECORD_ID);

            $classPk = $this->class->getDbConnection()->getSchema()->getTable($this->class->tableName())
                ->primaryKey;

            $constraint = 'fl_'.md5($this->tableName() . '_' . self::RECORD_ID .
                '_' . $this->class->tableName() . '_' . $classPk . '_fk');

            $command->addForeignKey($constraint,$this->tableName(),self::RECORD_ID,
                $this->class->tableName(),$classPk, 'CASCADE','CASCADE');
        }

        $codePath = Yii::getPathOfAlias($this->relationClassPath());
        $classPath = $codePath . '/' . $this->relationClass() . '.php';

        if(!file_exists($classPath)) {
            $codeRelationClass = $this->relationClass();
            $codeType = $this->type;
            $tableName = $this->tableName();

            $code =<<<PHP
<?php
/**
 * Class {$codeRelationClass} is a table for items values.
 * This code was automatically generated by YzItemValue class. DO NOT CHANGE THIS FILE!
 * @property int \$record_id
 * @property {$codeType} \$value
 */
class {$codeRelationClass} extends YzItemValueRecordTable
{
    /**
     * @param string \$className
     * @return CActiveRecord
     * @throws CException
     */
    public static function model(\$className=__CLASS__)
    {
        return parent::model(\$className);
    }

    /**
     * @return string
     */
    public function tableName()
    {
        return '{$tableName}';
    }
}

PHP;
            if(!file_exists($codePath)) {
                mkdir($codePath, 0755, true);
            }
            file_put_contents($classPath, $code);
        }

        Yii::import($this->relationClassPath() . '.' . $this->relationClass());
    }

    /**
     * @return string
     */
    public function relationClass()
    {
        $codeClass = get_class($this->class);
        $codeItem = $this->item;
        return "YzItemValue_{$codeClass}_{$codeItem}_Table";
    }

    /**
     * @return string
     */
    public function relationClassPath()
    {
        return 'application.runtime.YzItemValue';
    }
}

/**
 * Class YzItemValueRecordTable
 * @property int $record_id
 * @property mixed $value
 */
abstract class YzItemValueRecordTable extends CActiveRecord
{

}