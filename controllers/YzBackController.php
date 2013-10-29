<?php

class YzBackController extends YzController
{
    public $layout='yzAdmin.views.layouts.column2';

    public $breadcrumbs;

    /**
     * Action buttons for admin list
     * @var array
     */
    public $actions;

    /**
     * Pointer to the cache component of the application.
     * Set this property to null, if you want to disable caching
     * @var string
     */
    public $cache = 'cache';

    /**
     * @var AdminModule
     */
    protected $_adminModule;

    protected $_assetsUrl;

    protected $_scriptsStack = array();

    public function init()
    {
        /**
         * We must init Yz admin module due to have access of it properties,
         * `yzAdmin` path, etc.
         */
        $this->_adminModule = Yii::app()->getModule('admin');

        // Redefining components for admin panel
        Yii::app()->setComponents(array(
            'authManager' => array(
                'class' => 'yzAdmin.components.YzAdminAuthManager',
            ),
            'user' => array(
                'class' => 'yzAdmin.components.YzAdminWebUser',
            ),
        ));

        Yz::get()->registerBootstrap();
    }

    public function filters()
    {
        return array(
            'accessControl',
//            array(
//                'yzAdmin.components.YzAdminAccessFilter',
//                'allowedActions' => $this->allowedActions(),
//            ),
        );
    }

    public function accessRules()
    {
        return array(
            array('allow',
                'expression' => array($this,'checkAccess'),
            ),
            array('deny',
                'users' => array('@'),
                'deniedCallback' => array($this, 'loginRequired'),
            ),
            array('deny'),
        );
    }

    /**
     * @param YzAdminWebUser $user
     * @return bool
     */
    public function checkAccess($user)
    {
        $allow = true;

        // Initialize the authorization item as an empty string
        $authItem = '';

        // Append the module id to the authorization item name
        // in case the controller called belongs to a module
        if( ($module = $this->getModule())!==null )
            $authItem .= ucfirst($module->id);

        // Check if user has access to the controller
        if( $authItem == '' || $user->checkAccess($authItem.'.*') === false )
        {
            // Append the controller id to the authorization item name
            $authItem .= '.'.ucfirst($this->id);

            // Check if user has access to the controller
            if( $user->checkAccess($authItem.'.*') === false )
            {
                // Append the action id to the authorization item name
                $authItem .= '.'.ucfirst($this->action->id);

                // Check if the user has access to the controller action
                if( $user->checkAccess($authItem) === false )
                    $allow = false;
            }
        }

        return $allow;
    }

    public function accessDenied($message = null)
    {
        // TODO This part of code was copied from Rights module, have to rewrite?
        if( $message===null )
            $message = Yii::t('Yz.t9n', 'You are not authorized to perform this action.');

        $user = Yii::app()->getUser();
        if( $user->isGuest===true )
            $user->loginRequired();
        else
            throw new CHttpException(403, $message);

    }

    /**
     * @param CAccessRule $rule
     * @param YzAdminWebUser $user
     */
    public function loginRequired($rule)
    {
        Yii::app()->user->loginRequired();
    }

    /**
     * Common action for togging model's attributes
     * @param string $id
     * @param string $attribute
     * @throws CHttpException
     */
    public function actionToggle($id, $attribute)
    {
        if(method_exists($this,'loadModel') === false)
            throw new CHttpException(500,'Controller is not configured properly');

        $toggled = $this->getToggledAttributes();
        if($toggled === array())
            throw new CHttpException(500,'Model does not have toggled attributes');

        /** @var CActiveRecord $model */
        $model = $this->loadModel($id);
        if(!in_array($attribute, $toggled))
            YzAsyncResponse::failure();

        $model->{$attribute} = !($model->{$attribute});
        $model->save();

        YzAsyncResponse::success();
    }

    /**
     * @return array
     */
    protected function getToggledAttributes()
    {
        return array();
    }
}