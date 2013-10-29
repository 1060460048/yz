<?php

class YzFrontController extends YzController
{
    /**
     * @var string the default layout for the controller view. Defaults to '//layouts/column1',
     * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
     */
    public $layout = '//layouts/column1';
    /**
     * @var array context menu items. This property will be assigned to {@link CMenu::items}.
     */
    public $menu=array();
    /**
     * @var string context menu view. If this is not null, than $menu will be used
     */
    public $menuView=null;
    /**
     * @var string|array If string than it is the path to widget, witch will be used
     * to display menu block. If array - configuration for widget
     */
    public $menuWidget=null;
    /**
     * @var array the breadcrumbs of the current page. The value of this property will
     * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
     * for more details on how to specify this property.
     */
    public $breadcrumbs=array();
}