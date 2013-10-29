<?php

class YzHtml extends CHtml
{
    public static function resolveIdSafe($model, $attribute)
    {
        $tmp = $attribute;
        $htmlOptions = array();

        self::resolveNameID($model, $tmp, $htmlOptions);

        return $htmlOptions['id'];
    }

    public static function resolveNameSafe($model, $attribute)
    {
        $tmp = $attribute;
        $htmlOptions = array();

        self::resolveNameID($model, $tmp, $htmlOptions);

        return $htmlOptions['name'];
    }

    /**
     * Toggles HTML elements, based on model's attribute value.
     * To find witch element to toggle function uses jquery selector
     * '.field-{id} + .val-{value}'
     * @param CModel $model
     * @param string $attribute
     * @param string $hiddenClass
     * @param string $selector
     */
    public static function toggleElements($model,$attribute,$hiddenClass='hidden',$selector='')
    {
        $name = self::resolveNameSafe($model,$attribute);
        $id = self::resolveIdSafe($model,$attribute);

        $js=<<<EOD
$('input[name="{$name}"]:radio,select[name="{$name}"]').change(function(){
    $('.field-{$id}'){$selector}.addClass('{$hiddenClass}');
    $('.field-{$id}').filter('.val-'+$(this).val()){$selector}.removeClass('{$hiddenClass}');
});

$('input[name="{$name}"]:checkbox').change(function(){
    $('.field-{$id}'){$selector}.addClass('{$hiddenClass}');
    if($(this).prop('checked')) {
        $('.field-{$id}').filter('.val-1'){$selector}.removeClass('{$hiddenClass}');
    } else {
        $('.field-{$id}').filter('.val-0'){$selector}.removeClass('{$hiddenClass}');
    }
});

$('input[name="{$name}"]:radio:checked,input[name="{$name}"]:checkbox:checked,select[name="{$name}"]').change();
EOD;
        $jsCommon=<<<EOD
$('label').click(function(){
    $('input[id="'+$(this).prop('for')+'"]:radio').change();
});
EOD;


        /** @var $cs CClientScript */
        $cs=Yii::app()->getClientScript();
        $cs->registerCoreScript('jquery');
        $cs->registerScript('YzHtml.toggle.'.$id,$js);
        $cs->registerScript('YzHtml.toggle',$jsCommon);
    }

    /**
     * Returns selector, that can be used by {@see toggleElements} function.
     * @todo Add filtration for value or migrate to data-* attributes
     * @param CModel $model
     * @param string $attribute
     * @param string|array $value
     * @return string
     */
    public static function getToggleSelector($model,$attribute,$value)
    {
        $name = self::resolveNameSafe($model,$attribute);
        $id = self::resolveIdSafe($model,$attribute);

        if(is_array($value))
            $value = implode(' ',array_map(create_function('$v','return "val-".$v;'),$value));
        else
            $value = 'val-'.$value;

        return "field-{$id} {$value}";
    }

    /**
     * Adds ability to specify type of the field
     * @param CModel $model
     * @param string $attribute
     * @param array $htmlOptions
     * @return string
     */
    public static function activeTextField($model, $attribute, $htmlOptions = array())
    {
        self::resolveNameID($model,$attribute,$htmlOptions);
        self::clientChange('change',$htmlOptions);
        $type = isset($htmlOptions['type'])?$htmlOptions['type']:'text';
        return self::activeInputField($type,$model,$attribute,$htmlOptions);
    }


}