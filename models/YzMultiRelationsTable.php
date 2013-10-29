<?php

/**
 * Class YzMultiRelationsTable implements ability of accessing tables that can be related to different
 * tables by special fields
 */
class YzMultiRelationsTable extends YzBaseModel
{
    const OWNER_CLASS = '';

    protected $ownerTypeAttribute = 'owner_type';
    protected $ownerIdAttribute = 'owner_id';

    /**
     * @param int $owner_id
     * @return $this
     */
    public function byOwner($owner_id)
    {
        $criteria = new CDbCriteria();
        $criteria->condition = "{$this->tableAlias}.{$this->ownerIdAttribute} = :ownerId";
        $criteria->params = array(
            ':ownerId' => $owner_id,
        );
        $this->dbCriteria->mergeWith($criteria);
        return $this;
    }

    /**
     * @param CActiveRecord $owner
     * @return $this
     */
    public function factory($owner)
    {
        return call_user_func(array($this->getClassName($owner), 'model'));
    }

    /**
     * @param CActiveRecord $owner
     * @return string
     */
    public function getClassName($owner)
    {
        $baseClass = get_class($this);
        $className = get_class($owner) . $baseClass;
        $classRoute = 'application.runtime.YzMultiRelationsTable.'.$className;

        if(!class_exists($className,false)) {
            $this->generateClass($owner);
            Yii::import($classRoute);
        }

        return $className;
    }

    /**
     * @param CActiveRecord $owner
     * @return $this
     */
    public function newInstance($owner)
    {
        $className = $this->getClassName($owner);
        return new $className;
    }

    protected function generateClass($owner)
    {
        $baseClass = get_class($this);
        $ownerClass = get_class($owner);
        $className = $ownerClass . $baseClass;
        // TODO Optimize variables
        $classRoute = 'application.runtime.YzMultiRelationsTable.'.$className;
        $classPath = Yii::getPathOfAlias('application.runtime.YzMultiRelationsTable');
        $classFilePath = Yii::getPathOfAlias($classRoute) . '.php';

        if(!file_exists($classFilePath)) {
            $phpCode =<<<PHP
<?php

/**
 * Class {$className}
 */
class {$className} extends {$baseClass}
{
    const OWNER_CLASS = '{$ownerClass}';
    protected \$baseClassName = '{$baseClass}';

    /**
	 * Returns the static model of the specified AR class.
	 * @param string \$className active record class name.
	 * @return Files the static model class
	 */
	public static function model(\$className=__CLASS__)
	{
		return parent::model(\$className);
	}

    public function defaultScope()
    {
        return array(
            'condition' => \$this->getTableAlias(false,false).".{\$this->ownerTypeAttribute} = :ownerType",
            'params' => array(
                ':ownerType' => self::OWNER_CLASS,
            ),
        );
    }

    protected function beforeSave()
    {
        \$this->{\$this->ownerTypeAttribute} = self::OWNER_CLASS;
        return parent::beforeSave();
    }

    public function deleteAll(\$condition = '', \$params = array())
    {
        \$criteria=\$this->getCommandBuilder()->createCriteria(\$condition,\$params);
        \$criteria->addCondition('owner_type = :ownerType');
        \$criteria->params = array_merge(\$criteria->params,array(
            ':ownerType' => self::OWNER_CLASS,
        ));
        return parent::deleteAll(\$criteria, \$params);
    }

    public function deleteAllByAttributes(\$attributes, \$condition = '', \$params = array())
    {
        \$criteria=\$this->getCommandBuilder()->createCriteria(\$condition,\$params);
        \$criteria->addCondition('owner_type = :ownerType');
        \$criteria->params = array_merge(\$criteria->params,array(
            ':ownerType' => self::OWNER_CLASS,
        ));
        return parent::deleteAllByAttributes(\$attributes, \$criteria, \$params);
    }

	public function factory(\$owner){
	    throw new CException("This method can not be called from the child class");
	}

	public function getClassName(\$owner){
	    throw new CException("This method can not be called from the child class");
	}
}
PHP;
            if(!file_exists($classPath))
                mkdir($classPath, 0755, true);

            file_put_contents($classFilePath, $phpCode);
        }
    }
}