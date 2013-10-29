<?php

/**
 * @property boolean $isShowInAdminPanel
 * @property array|null $adminNavigation
 * @property boolean $isAutoDiscoverAuthItems
 * @property array $urlRules
 */
class YzWebModule extends YzBaseWebModule
{
    /**
     *  @var int порядок следования модуля в меню панели управления (сортировка)
     */
    public $adminMenuOrder = 0;

    /**
     * Custom links for index controller in backed ('admin')
     * @var array
     */
    public $adminCustomLinks = array();

    /**
     *  @var array правила маршрутизации модуля (collected automatically)
     */
    protected $_urlRules = array();

    /**
     *  @return string текущая версия модуля
     */
    public function getVersion()
    {
        return '0.1';
    }

    /**
     *  @return string веб-сайт разработчика модуля или страничка самого модуля
     */
    public function getUrl()
    {
        return false;
    }

    /**
     *  @return string имя автора модуля
     */
    public function getAuthor()
    {
        return Yii::t('Yz.t9n', 'Yz Engine Team');
    }

    /**
     *  @return string контактный email автора модуля
     */
    public function getAuthorEmail()
    {
        return '';
    }

    /**
     *  @return null|string ссылка которая будет отображена в панели управления
     *  как правило, ведет на страничку для администрирования модуля
     */
    public function getAdminPageLink()
    {
        return strtolower($this->id) . '/backend/' . strtolower($this->defaultController);
    }

    /**
     * @return array если модуль должен добавить несколько ссылок в панель управления - укажите массив
     * @example
     *
     * public function getNavigation()
     * {
     *       return array(
     *           Yii::t('pages','List')  => '/blog/blogAdmin/admin/',
     *           Yii::t('pages','New page') => '/blog/postAdmin/admin/',
     *           array(
     *              'label' => Yii::t('pages','List'),
     *              'route' => array('/blog/postAdmin/admin/'),
     *              'icon' => 'list'
     *           ),
     *      );
     * }
     *
     */
    public function getAdminNavigation()
    {
        return false;
    }

    /**
     *   @return array или false
     *   Работосопособность модуля может зависеть от разных факторов: версия php, версия Yii, наличие определенных модулей и т.д.
     *   В этом методе необходимо выполнить все проверки.
     *   @example
     *
     *   if (!$this->uploadDir)
     *        return array('type' => YWebModule::CHECK_ERROR, 'message' => Yii::t('image', 'Пожалуйста, укажите каталог для хранения изображений! {link}', array('{link}' => CHtml::link(Yii::t('image', 'Изменить настройки модуля'), array('/yupe/backend/modulesettings/', 'module' => $this->id)))));
     *
     *   Данные сообщения выводятся на главной странице панели управления
     *
     */
    public function checkSelf()
    {
        return true;
    }

    /**
     *  @return string каждый модуль должен принадлежать одной категории, именно по категорям делятся модули в панели управления
     */
    public function getName()
    {
        return Yii::t('Yz.t9n', 'Yz Module');
    }

    /**
     *  @return array массив лейблов для параметров (свойств) модуля. Используется на странице настроек модуля в панели управления.
     */
    public function getParamsLabels()
    {
        return array('adminMenuOrder' => Yii::t('Yz.t9n', 'Menu order'), );
    }

    /**
     *  @return array массив параметров модуля, которые можно редактировать через панель управления (GUI)
     */
    public function getEditableParams()
    {
        return array(
            'adminMenuOrder' => 'integer',
        );
    }

    /**
     *  @return int порядок следования модуля в меню панели управления (сортировка)
     */
    public function getAdminMenuOrder()
    {
        return $this->adminMenuOrder;
    }

    /**
     *  @return bool показать или нет модуль в панели управления
     */

    public function getIsShowInAdminPanel()
    {
        return true;
    }

    public function getIsAutoDiscoverAuthItems()
    {
        return true;
    }

    public function getAuthItems()
    {
        return array();
    }

    public function getUrlRules()
    {
        return $this->_urlRules;
    }

    public function setUrlRules($urlRules)
    {
        $this->_urlRules = $urlRules;
    }

    /**
     * @return string icon for module 'user'
     */
    public function getIcon()
    {
        return null;
    }

    /**
     *  Module initialization
     */
    public function init()
    {
        /** @var $settings YzModuleSettings */
        $settings = YzModuleSettings::model()->fetchModuleSettings($this->id);

        if($settings)
        {
            $editableParams = $this->getEditableParams();

            foreach ($editableParams as $paramName => $type)
            {
                if(isset($settings->parameters[$paramName]))
                    $this->{$paramName} = $settings->parameters[$paramName];
            }
        }

        parent::init();
    }

    public function getCacheId()
    {
        return $this->getId();
    }

    public function getAssetsUrl($path = null)
    {
        if($path === null) {
            $modules = Yii::app()->getModules();
            $assetsPath = dirname(Yii::getPathOfAlias($modules[$this->id]['class'])) . '/assets';
        } else
            $assetsPath = Yii::getPathOfAlias($path);

        $assetsUrl = Yii::app()->assetManager->publish($assetsPath, false, -1, Yz::get()->alwaysUpdateAssets);
        return $assetsUrl;
    }

    public function registerComponent($name, $appComponentName, $componentConfig = null)
    {
        if($this->hasComponent($name))
            return;

        if($appComponentName === null) {
            if($componentConfig === null)
                throw new CException('You must define '.$name.' component or use application-level component instead');
            else
                $this->setComponent($name, $componentConfig);
        } else {
            $this->setComponent($name, Yii::app()->{$appComponentName});
        }
    }
}