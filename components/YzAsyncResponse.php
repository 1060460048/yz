<?php

class YzAsyncResponse extends CComponent
{
    public static function raw($data)
    {
        echo CJSON::encode($data);
        Yii::app()->end();
    }

    public static function labelValue($data, $labelField = 'label', $valueField = 'value')
    {
        if(!is_array($data))
            $data = array($data);

        $result = array();

        foreach($data as $element) {
            $result[] = array(
                'label' => $element[$labelField],
                'value' => $element[$valueField],
            );
        }

        static::raw($result);
    }

    /**
     * @param mixed $data
     */
    public static function data($data)
    {
        static::raw(array(
            'result' => true,
            'data' => $data,
        ));
    }

    /**
     * @param string $message
     */
    public static function success($message = null,$code=null)
    {
        static::raw(array(
            'result' => true,
            'message' => $message,
            'code' => $code,
        ));
    }

    /**
     * @param string $message
     */
    public static function failure($message = null,$code=null)
    {
        static::raw(array(
            'result' => false,
            'message' => $message,
            'code' => $code,
        ));
    }
}