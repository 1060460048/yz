<?php

/**
 * This is the model class for table "{{settings_groups}}".
 *
 * The followings are the available columns in table '{{settings_groups}}':
 * @property string $id
 * @property string $name
 * @property string $title
 * @property string $create_date
 * @property string $update_date
 *
 * @property array $settings
 */
class YzSettingsGroups extends YzBaseModel
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return YzSettingsGroups the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return '{{settings_groups}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name, title, create_date, update_date', 'required'),
			array('name, title', 'length', 'max'=>128),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, name, title, create_date, update_date', 'safe', 'on'=>'search'),
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
            'settings' => array(self::HAS_MANY, 'YzSettings', 'group_id'),
		);
	}

    /**
    * @return array behaviors.
    */
    public function behaviors()
    {
        return CMap::mergeArray(
            parent::behaviors(),
            array(
                'CreateUpdateTimestamp' => array(
                    'class' => 'zii.behaviors.CTimestampBehavior',
                    'createAttribute' => 'create_date',
                    'updateAttribute' => 'update_date',
                ),
            )
        );
    }

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => Yii::t('Yz.t9n','Name'),
			'title' => Yii::t('Yz.t9n','Title'),
			'create_date' => Yii::t('Yz.t9n','Create Date'),
			'update_date' => Yii::t('Yz.t9n','Update Date'),
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

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('create_date',$this->create_date,true);
		$criteria->compare('update_date',$this->update_date,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}