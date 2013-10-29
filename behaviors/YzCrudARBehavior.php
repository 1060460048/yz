<?php
/**
 * @property string $behaviorName Current behavior name, used to add errors to it's property
 * @property string $validationErrorMessage Default error message for current behavior
 * @property string $ownerType Type of the owner model. Default value is <code>get_class($this->owner)</code>
 */
abstract class YzCrudARBehavior extends CActiveRecordBehavior
{
    /**
     * @var bool Whether to enable CRUD operations or not
     */
    public $enableCrud = true;
    /**
     * @var bool Whether to enable CRUD only in backend (this protects from malformed request from
     * frontend)
     */
    public $allowCrudOnlyInBackend = true;
    /**
     * @var string
     */
    protected $_behaviorName = null;
    /**
     * @var string
     */
    protected $_validationErrorMessage = null;
    /**
     * @var string
     */
    protected $_ownerType = null;

    public function afterValidate($event)
    {
        parent::afterValidate($event);

        $valid = true;

        if($this->getIsCrudAllowed()) {
            $valid = $this->onValidate();
        }

        if($valid == false) {
            $this->owner->addError($this->getBehaviorName(),$this->getValidationErrorMessage());
        }
    }

    public function afterSave($event)
    {
        parent::afterSave($event);

        if($this->getIsCrudAllowed()) {
            if($this->owner->isNewRecord) {
                $this->onInsert();
            } else {
                $this->onUpdate();
            }
        }
    }

    protected function beforeDelete($event)
    {
        parent::beforeDelete($event);

        if($this->getIsCrudAllowed()) {
            $this->onDelete();
        }
    }


    /**
     * @param string $behaviorName
     */
    public function setBehaviorName($behaviorName)
    {
        $this->_behaviorName = $behaviorName;
    }

    /**
     * @return string
     */
    public function getBehaviorName()
    {
        return $this->_behaviorName;
    }

    /**
     * @param string $validationErrorMessage
     */
    public function setValidationErrorMessage($validationErrorMessage)
    {
        $this->_validationErrorMessage = $validationErrorMessage;
    }

    /**
     * @return string
     */
    public function getValidationErrorMessage()
    {
        if($this->_validationErrorMessage === null) {
            $this->_validationErrorMessage =
                Yii::t('AdminModule.t9n', 'There are errors in {name} property',array(
                    '{name}' => $this->owner->getAttributeLabel($this->getBehaviorName()),
                ));
        }
        return $this->_validationErrorMessage;
    }

    /**
     * @param string $ownerType
     */
    public function setOwnerType($ownerType)
    {
        $this->_ownerType = $ownerType;
    }

    /**
     * @return string
     */
    public function getOwnerType()
    {
        if($this->_ownerType === null) {
            $this->_ownerType = get_class($this->owner);
        }
        return $this->_ownerType;
    }

    /**
     * @return bool
     */
    protected function onValidate()
    {
        return true;
    }

    /**
     * Triggered on new record created
     */
    protected function onInsert()
    {
        $this->onSave();
    }

    /**
     * Triggered on updating record
     */
    protected function onUpdate()
    {
        $this->onSave();
    }

    /**
     * Triggered both on insert and on update events, but if you redefine
     * {@see onInsert} or {@see onUpdate} than this method will not be called
     */
    protected function onSave()
    {

    }

    /**
     * Triggered on deleting record
     */
    protected function onDelete()
    {

    }

    /**
     * Whether CRUD is allowed or not, depended on {@see $enableCrud} and
     * {@see $allowCrudOnlyInBackend} properties
     * @return bool
     */
    protected function getIsCrudAllowed()
    {
        return $this->enableCrud &&
            (!$this->allowCrudOnlyInBackend || ($this->allowCrudOnlyInBackend && Yz::isDeveloperMode()));
    }

    /**
     * Returns model instance for classes, that has owner_type and owner_id properties
     * @param string $modelName
     * @param string $relationName
     * @param bool $createNew
     * @return CActiveRecord
     */
    protected function getTypedModel($modelName, $relationName = null, $createNew = true)
    {
        if($relationName === null)
            $relationName = lcfirst($modelName);

        $model = null;

        if($this->owner->metaData->hasRelation($relationName)) {
            $model = $this->owner->{$relationName};
            if($model !== null)
                return $model;
        }

        if($createNew && $this->owner->isNewRecord) {
            $model = new $modelName;
            $model->owner_type = $this->getOwnerType();
        } else {
            $model = call_user_func(array($modelName,'model'))->findByAttributes(array(
                'owner_type' => $this->getOwnerType(),
                'owner_id' => $this->owner->getPrimaryKey(),
            ));
            if($model === null && $createNew) {
                $model = new $modelName;
                $model->owner_type = $this->getOwnerType();
            }
        }
        return $model;
    }
}