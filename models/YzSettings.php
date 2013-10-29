<?php

/**
 * This is the model class for table "{{settings}}".
 *
 * The followings are the available columns in table '{{settings}}':
 * @property string $id
 * @property string $group_id
 * @property string $name
 * @property string $title
 * @property string $type
 * @property integer $value_int
 * @property float $value_double
 * @property string $value_string
 * @property integer|float|string|boolean $value
 */
class YzSettings extends YzBaseModel
{
	public $value;

    /**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return YzSettings the static model class
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
		return '{{settings}}';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		$rules = array(
			array('group_id, name, title, value_int, value_double, value_string', 'required'),
			array('value_int', 'numerical', 'integerOnly'=>true),
			array('value_double', 'numerical'),
			array('group_id', 'length', 'max'=>10),
			array('name, title', 'length', 'max'=>128),
			array('type', 'in', 'range' => $this->attributeValues('type', true)),
            array('type', 'default', 'value' => 'string'),
			array('value_string', 'length', 'max'=>256),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, group_id, name, title, type, value_int, value_double, value_string', 'safe', 'on'=>'search'),
		);

        switch( $this->type ) {
            default:
            case 'string': $rules[] = array('value','length','max'=>256); break;
            case 'integer': $rules[] = array('value', 'numerical', 'integerOnly'=>true); break;
            case 'double': $rules[] = array('value', 'numerical'); break;
            case 'email': $rules[] = array('value', 'email'); break;
            case 'boolean': $rules[] = array('value', 'boolean'); break;
        }

        return $rules;
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
            'group' => array(self::BELONGS_TO, 'YzSettingsGroups', 'group_id'),
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
			'group_id' => Yii::t('Yz.t9n','Group'),
			'name' => Yii::t('Yz.t9n','Name'),
			'title' => Yii::t('Yz.t9n','Title'),
			'type' => Yii::t('Yz.t9n','Type'),
			'value_int' => Yii::t('Yz.t9n','Value Int'),
			'value_double' => Yii::t('Yz.t9n','Value Double'),
			'value_string' => Yii::t('Yz.t9n','Value String'),
		);
	}

    public function attributesValues()
    {
        return array(
            'type' => array(
                'integer' => Yii::t('Yz.t9n','Integer'),
                'double' => Yii::t('Yz.t9n','Double'),
                'boolean' => Yii::t('Yz.t9n','Boolean'),
                'string' => Yii::t('Yz.t9n','String'),
                'email' => Yii::t('Yz.t9n','Email'),
            ),
        );
    }

    protected function beforeSave()
    {
        $this->value_double = 0.0;
        $this->value_int = 0;
        $this->value_string = 0;

        switch($this->type) {
            case 'integer':
                $this->value_int = intval($this->value);
                break;
            case 'double':
                $this->value_double = floatval($this->value);
                break;
            case 'string':
            case 'email':
                $this->value_string = "{$this->value}";
                break;
            case 'boolean':
                $this->value_int = $this->value ? 1 : 0;
                break;
        }
    }

    protected function afterFind()
    {
        switch($this->type) {
            case 'integer':
                $this->value = $this->value_int;
                break;
            case 'double':
                $this->value = $this->value_double;
                break;
            case 'string':
            case 'email':
                $this->value = $this->value_string;
                break;
            case 'boolean':
                $this->value = ($this->value_int == 1);
        }
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
		$criteria->compare('group_id',$this->group_id,true);
		$criteria->compare('name',$this->name,true);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('value_int',$this->value_int);
		$criteria->compare('value_double',$this->value_double);
		$criteria->compare('value_string',$this->value_string,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
}