<?php

class YzPhoneValidator extends CValidator
{
    /**
     * Validates a single attribute.
     * This method should be overridden by child classes.
     * @param CModel $object the data object being validated
     * @param string $attribute the name of the attribute to be validated.
     */
    protected function validateAttribute($object, $attribute)
    {
        $phone = $object->{$attribute};
        $phone = YzPhoneHelper::normalizePhone($phone);

        if(strlen($phone) != 10)
            $object->addError($attribute, Yii::t('Yz.t9n', 'You entered incorrect phone number'));
    }

}