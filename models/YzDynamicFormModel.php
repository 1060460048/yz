<?php

/**
 * Class YzDynamicFormModel implements functionality to dynamically
 * control list of model's attributes
 */
class YzDynamicFormModel extends CFormModel
{
    protected $_properties = array();
    protected $_rules = array();

    public function attributeNames()
    {
        return array_merge(parent::attributeNames(),array_keys($this->_properties));
    }

    public function rules()
    {
        return $this->_rules;
    }

    public function setProperties($properties)
    {
        $this->_properties = $properties;
    }

    public function getProperties()
    {
        return $this->_properties;
    }

    public function setRules($rules)
    {
        $this->_rules = $rules;
    }

    public function getRules()
    {
        return $this->_rules;
    }

    public function __get($name)
    {
        if(isset($this->_properties[$name]))
            return $this->_properties[$name];
        else
            return parent::__get($name);
    }

    public function __set($name, $value)
    {
        if(isset($this->_properties[$name]))
            return ($this->_properties[$name] = $value);
        else
            return parent::__set($name, $value);
    }

    public function __isset($name)
    {
        if(isset($this->_properties[$name]))
            return true;
        else
            return parent::__isset($name);
    }

    public function __unset($name)
    {
        if(isset($this->_properties[$name]))
            unset($this->_properties[$name]);
        else
            parent::__unset($name);
    }
}