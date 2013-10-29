<?php

class YzXmlItem extends CAttributeCollection
{
    /**
     * @var CAttributeCollection
     */
    public $attributes;
    /**
     * @var mixed
     */
    protected $_value;

    /**
     * @var string
     */
    protected $_type;

    public function __construct($type = null, $attributes = array(), $value = null)
    {
        parent::__construct();
        $this->_type = $type;
        $this->attributes = new CAttributeCollection();
        $this->attributes->mergeWith($attributes, false);
        $this->_value = $value;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->_type = $type;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->_type;
    }

    public function fromArray($array, $assign = null)
    {
        if($assign == null)
            $assign = $this;

        foreach($array as $key => $item) {
            if(intval($key) === $key)
                $key = 'item_'.$key;
            switch(gettype($item)) {
                case 'array':
                    $assign->{$key} = $assign->fromArray($item, new YzXmlItem());
                    break;
                default:
                    $assign->{$key} = $item;
            }
        }

        return $assign;
    }

    public function generate($addSelf = true)
    {
        if(is_string($addSelf)) {
            $type = $addSelf;
        } else
            $type = $this->getType();
        if($addSelf && $type !== null) {
            $xml = '<'.$type;
            if(count($this->attributes) > 0) {
                foreach($this->attributes as $name=>$value) {
                    $xml .= " {$name}=\"".CHtml::encode($value)."\"";
                }
            }
            $xml .= '>';
        } else
            $xml = '';
        if($addSelf && $this->_value !== null) {
            $xml .= CHtml::encode($this->_value);
        } else {
            foreach($this->getKeys() as $key) {
                $item = $this->itemAt($key);

                switch(gettype($item)) {
                    case 'object':
                    case 'array':
                        if($item instanceof YzXmlItem) {
                            $xml .= $item->generate($key);
                        } elseif($item instanceof CList || gettype($item) == 'array') {
                            $xml .= "<{$key}>";
                            foreach($item as $subItem) {
                                if($subItem instanceof YzXmlItem) {
                                    $xml .= $subItem->generate();
                                }
                            }
                            $xml .= "</{$key}>";
                        }
                        break;
                    case 'boolean':
                        $item = $item ? '1':'0';
                    default:
                        if($item === '')
                            $xml .= "<{$key}/>";
                        else
                            $xml .= "<{$key}>".CHtml::encode($item)."</{$key}>";
                }
            }
        }
        if($addSelf && $type !== null)
            $xml .= '</'.$type.'>';

        return $xml;
    }

    public function xml()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8" ?>';
        $xml .= $this->generate();
        return $xml;
    }
}