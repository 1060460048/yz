<?php

class YzAutoNameBehavior extends CActiveRecordBehavior
{
    /**
     * @var string
     */
    public $attribute = 'name';

    /**
     * Possible values: template, translit
     * @var string
     */
    public $method = 'template';

    public $template = 'record_{id}';

    public $translitAttribute = '';

    /**
     * Set name value only if {@see $attribute} attribute of model is empty
     * @var bool
     */
    public $onlyOnEmpty = true;

    public $on = '';

    public function afterSave($event)
    {
        if(!is_array($this->on))
            $this->on = preg_split('/[ ,]+/',$this->on, -1, PREG_SPLIT_NO_EMPTY);

        if(empty($this->on) || in_array($this->owner->scenario, $this->on)) {
            if($this->onlyOnEmpty == false || $this->owner->{$this->attribute} == '') {
                switch($this->method) {
                    default:
                    case 'template':
                        $attributeValue = strtr($this->template, array(
                            '{id}' => $this->owner->getPrimaryKey(),
                        ));
                        break;
                    case 'translit':
                        // TODO Realize this setting
                        throw new CException('This setting is not realized yet');
                        break;
                }
                $this->owner->{$this->attribute} = $attributeValue;
                $this->owner->updateByPk($this->owner->getPrimaryKey(),
                    array($this->attribute => $attributeValue));
            }
        }
    }


}