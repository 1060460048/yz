<?php

class YzMultiEmailsValidator extends CEmailValidator
{
    public $delimiter = ';';

    public $min = 1;

    public $max = 1;

    protected function validateAttribute($object, $attribute)
    {
        $values = $object->{$attribute};

        if($this->allowEmpty && $this->isEmpty($values))
            return;

        $values = explode($this->delimiter, $values);
        $count = count($values);

        if ($count > $this->max && $this->max != 0)
        {
            $message=$this->message!==null?$this->message:Yii::t('yz.t9n', 'A maximum of {value} email(s) allowed.');
            $this->addError($object,$attribute,$message, array('{value}'=>$this->max));
            return;
        }

        if ($count < $this->min && $this->min != 0)
        {
            $message=$this->message!==null?$this->message:Yii::t('yz.t9n', 'At least {value} email(s) required.');
            $this->addError($object,$attribute,$message, array('{value}'=>$this->min));
            return;
        }

        foreach($values as $value)
        {
            $value = trim($value);

            if (!parent::validateValue($value))
            {
                if (!empty($value))
                {
                    $message=$this->message!==null?$this->message:Yii::t('yz.t9n', '{value} is not a valid email address.');
                    $this->addError($object,$attribute,$message, array('{value}'=>$value));
                }
            }
        }
    }

    public function validateValue($value)
    {
        $values = explode($this->delimiter, $value);

        $count = count($values);

        if ($count > $this->max && $this->max != 0)
        {
            return false;
        }

        if ($count < $this->min && $this->min != 0)
        {
            return false;
        }

        foreach($values as $value)
        {
            $value = trim($value);

            if (!parent::validateValue($value))
            {
                if (!empty($value))
                {
                    return false;
                }
            }
        }

        return true;
    }
}