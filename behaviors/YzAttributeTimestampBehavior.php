<?php


class YzAttributeTimestampBehavior extends CActiveRecordBehavior
{
    private $_formatter;

    public function __set($name, $value)
    {
        if($this->getOwner()->hasAttribute($name)) {
            $this->getOwner()->{$name} = strftime('%Y-%m-%d %H:%M:%S',$value);
        }
        else
            parent::__set($name, $value);
    }

    public function __get($name)
    {
        preg_match('/^([0-9a-zA-Z_]+)_(date|time|datetime)$/', $name, $m);
        if(!empty($m) && $this->getOwner()->hasAttribute($m[1]))
            return $this->getFormatter()->format(strtotime($this->getOwner()->{$m[1]}), $m[2]);
        elseif($this->getOwner()->hasAttribute($name)) {
            return strtotime($this->getOwner()->{$name});
        }
        else
            return parent::_get($name);
    }

    public function __isset($name)
    {
        if($this->getOwner()->hasAttribute($name))
            return true;
        else
            return parent::__isset($name);
    }

    public function setFormatter($formatter)
    {
        $this->_formatter = $formatter;
    }

    /**
     * @return CFormatter
     */
    public function getFormatter()
    {
        if($this->_formatter===null)
            $this->_formatter=Yii::app()->format;
        return $this->_formatter;
    }
}