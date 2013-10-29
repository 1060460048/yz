<?php

/**
 * YzRequiredValidator validates that the specified attribute does not have
 * empty or null value, if result of evaluating of {@see $rule} is true,
 * or owner's {@see $dependentAttribute} has {@see $dependentAttributeValue}
 */
class YzRequiredValidator extends CRequiredValidator
{
    /**
     * @var mixed Any valid php callback or string
     */
    public $rule = null;
    public $dependentAttribute = null;
    public $dependentAttributeValue = null;
    public $compareOperator = '=';

    protected function validateAttribute($object, $attribute)
    {
        $apply = true;
        if($this->rule !== null) {

            $apply = $this->evaluateExpression($this->rule, array(
                'object' => $object,
                'attribute' => $attribute,
            ));

        } elseif($this->dependentAttribute !== null) {

            switch($this->compareOperator) {
                default:
                case '=':case '==':case 'eq':case 'equal':
                    $apply = $object->{$this->dependentAttribute} == $this->dependentAttributeValue;
                    break;
                case '!=':case 'neq':case 'notequal':case 'not equal':
                    $apply = $object->{$this->dependentAttribute} != $this->dependentAttributeValue;
                    break;
            }

        }
        if($apply)
            parent::validateAttribute($object, $attribute);
    }


}