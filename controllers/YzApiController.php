<?php
/**
 * Class YzApiController implements base class for all controllers
 * that are intended to be used as api controllers
 */
class YzApiController extends YzController
{
    public function filters()
    {
        return array(
            'accessControl',
        );
    }

    public function accessRules()
    {
        return array(
            array('allow',
                'expression' => array($this,'checkAccess')
            ),
            array('deny')
        );
    }

    public function checkAccess($user)
    {
        return true;
    }
}