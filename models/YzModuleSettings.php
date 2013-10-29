<?php

/**
 * This is the model class for table "{{modules_settings}}".
 *
 * The followings are the available columns in table '{{modules_settings}}':
 * @property int $id
 * @property string $module_id
 * @property string $parameters
 * @property int $create_date
 * @property int $update_date
 */
class YzModuleSettings extends CActiveRecord
{
    /**
     * Returns the static model of the specified AR class.
     * @return YzModuleSettings the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return '{{modules_settings}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('module_id', 'required'),
            array('module_id', 'length', 'max' => 150),
            array('module_id','match','pattern' => '/^[a-zA-Z0-9_\-]+$/'),
            array('parameters','safe'),
            //array('param_name, param_value','match','pattern' => '/^[a-zA-Z0-9_\-]+$/'),
            array('id, module_id, param_name, param_value, create_date, update_date', 'safe', 'on' => 'search'),
        );
    }

    public function behaviors()
    {
        return array(
            'CreateUpdateTimestamp' => array(
                'class' => 'zii.behaviors.CTimestampBehavior',
                'createAttribute' => 'create_date',
                'updateAttribute' => 'update_date',
            ),
            'JSON' => array(
                'class' => 'yz.behaviors.YzJSONFieldBehavior',
                'attribute' => 'parameters',
            ),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            //'user' => array(self::BELONGS_TO, 'User', 'user_id'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => 'ID',
            'module_id' => Yii::t('Yz.t9n','Module ID'),
            'parameters' => Yii::t('Yz.t9n','Parameters'),
            'create_date' => Yii::t('Yz.t9n','Creation Date'),
            'update_date' => Yii::t('Yz.t9n','Change Date'),
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        // Warning: Please modify the following code to remove attributes that
        // should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('id', $this->id, true);
        $criteria->compare('module_id', $this->module_id, true);
        $criteria->compare('parameters', $this->parameters, true);
        $criteria->compare('create_date', $this->create_date, true);
        $criteria->compare('update_date', $this->update_date, true);

        return new CActiveDataProvider(get_class($this), array(
            'criteria' => $criteria,
            'pagination' => array(
                'pageSize' => 30,
            ),
        ));
    }

    /**
     * @return YzWebModule|null
     */
    public function getModule()
    {
        return Yz::get()->getModule($this->module_id);
    }

    /**
     * Получает настройки модуля
     *
     * @param string $module_id Идентификатор модуля
     * @return YzModuleSettings Экземпляры класса Settings, соответствующие запрошенным параметрам
     */
    public function fetchModuleSettings($module_id)
    {
        return YzModuleSettings::model()
            ->cache(1000, new YzCacheTags('YzModuleSettings.'.$module_id))
            ->findByAttributes(array(
                'module_id' => $module_id,
            ));
    }

    protected function beforeSave()
    {
        Yz::clearCache('YzModuleSettings.'.$this->module_id);
        return parent::beforeSave();
    }
}