<?php

class YzApiResponse extends YzAsyncResponse
{
    public static function raw($data)
    {
        $callback = isset($_GET['jsonp'])?$_GET['jsonp']:(isset($_GET['callback'])?$_GET['callback']:null);

        if(empty($callback)) {
            parent::raw($data);
        } else {
            header('Content-type:'.CFileHelper::getMimeTypeByExtension('response.js'));
            echo $callback.'('.CJSON::encode($data).');';
            Yii::app()->end();
        }
    }
}