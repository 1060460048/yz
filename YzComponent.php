<?php

/**
 * YzComponent class is the main component of Yz Engine.
 * This class initialize all required for Yz classes, components, etc.
 *
 * @property YzExtensionsLinker $extensionsLinker
 * @property-read bool $isConsoleMode
 * @property bool $alwaysUpdateAssets Whether to update assets on each request
 *
 * @author Pavel Agalecky <pavel.agalecky@gmail.com>
 */
class YzComponent extends CApplicationComponent
{
    /**
     * If we are in developer mode, some components and modules
     * provide more settings and possibilities
     *
     * @var bool
     */
    public $developerMode = true;

    /**
     * Indicates whether to check all needed for Yz Engine components
     * to be configured properly.
     *
     * @var bool
     */
    public $checkComponents = true;
    /**
     * This option allows YzComponent automatically init
     * all needed components for Yz Engine.
     * But this setting is not recommended due to:
     * <ol>
     * <li>immediately loading of all components instead of lazy loading
     * <li>default values, selected by YzComponent maybe not optimal
     *    for your application.
     * </ol>
     *
     * @var bool
     */
    public $initNeededComponents = false;

    /**
     * This option allows YzComponent initialize YzUrlRolesCollector
     * that will collect all custom rules from different Yz modules
     * @var bool
     */
    public $initRulesCollector = true;

    /**
     * Whether to use path to add compatibility with
     * some strange hostings, that doesn't have some functions.
     * Disable this if your hosting is compatible with Yz
     * @var bool
     */
    public $useCompatibilityPatch = true;

    /**
     * This option force use YzFormatter class, witch adds support
     * for translation of boolean type.
     * This option will be available until Yii adds support for this
     * @var bool
     */
    public $useYzFormatter = true;

    /**
     * Witch modules to exclude from been loaded by {@link getModules} function.
     * Usually they are ones, that are not children of {@link YzBaseWebModule} class.<br>
     * Default value: array('gii')
     * @var array
     */
    public $excludeModules = array(
        'gii',
    );

    /**
     * This setting is used to manipulate (ex. flush, etc.) all cache components
     * of the application at once. Default value is array('cache').
     * If you set this to null (or not array), only Yii::app()->cache component
     * will be controlled.
     * @var array
     */
    public $cacheComponents = array(
        'cache',
    );

    /**
     * Whether to register Yz extension folder and components.
     * This will register alias yzext, and will give you easy
     * access to this extension
     * @var bool
     */
    public $registerYzExt = true;

    /**
     * @var YzExtensionsLinker
     */
    protected $_extensionsLinker = array();

    /**
     * @var bool If we running in console application
     */
    protected $_isConsoleMode = null;

    /**
     * @var bool Whether to update assets on each request
     */
    protected $_alwaysUpdateAssets = null;

    /**
     * Cache for {@link getModule} and {@link getModules} functions
     * @var array
     */
    private $_modules = array();

    /**
     * Initialize the component
     */
    public function init()
    {
        // Register YzModel Engine alias
        if(Yii::getPathOfAlias('yz') === false)
            Yii::setPathOfAlias('yz', realpath(dirname(__FILE__)));

        // Set core import path for Yz Engine
        Yii::app()->setImport(array(
            'yz.*',
            'yz.controllers.*',
            'yz.components.*',
            'yz.models.*',
            'yz.helpers.*',
            'yz.widgets.YzWidget',
            'yz.widgets.YzInputWidget',
        ));

        // Loading compatibility
        if($this->useCompatibilityPatch)
            include_once(Yii::getPathOfAlias('yz.helpers') . DIRECTORY_SEPARATOR . 'compat.php');

        if($this->useYzFormatter)
            Yii::app()->setComponent('format',Yii::createComponent('yz.components.YzFormatter'));

        if($this->checkComponents) {
            // Checking for settings
            if(!Yii::app()->hasComponent('settings')) {
                if( $this->initNeededComponents )
                    Yii::app()->setComponent('settings', Yii::createComponent('yz.components.YzSettingsComponent'));
                else
                    throw new CException(Yii::t('Yz.t9n','You must define settings component to use Yz Engine'));
            }

            // Checking for yz module
            if(!Yii::app()->hasModule('yzSystem')) {
                if( $this->initNeededComponents || $this->getIsConsoleMode() )
                    Yii::app()->setModules(array('yzSystem'));
                else
                    throw new CException(Yii::t('Yz.t9n','You must define Yz system module to use Yz Engine'));
            }
        }

        if($this->registerYzExt) {
            Yii::import($this->extensionsLinker->getExtensionPath('YzExt').'.YzExt');
            YzExt::register();
        }

        if($this->initRulesCollector && !$this->getIsConsoleMode()) {
            Yii::app()->attachEventHandler('onBeginRequest', array('YzUrlRulesCollectorHelper','collect'));
        }

        parent::init();
    }

    /**
     * Returns list of application modules, that are {@link YzWebModule} childs
     *
     * @return array
     */
    public function getModules()
    {
        foreach( Yii::app()->getModules() as $moduleId => $moduleConfig) {
            if(isset($this->_modules[$moduleId]))
                continue;

            $this->getModule($moduleId);
        }

        return $this->_modules;
    }

    /**
     * Loads Yz Module to get access to it's setting
     * @param $moduleId string
     * @return YzWebModule|null
     */
    public function getModule($moduleId)
    {
        if(isset($this->_modules[$moduleId]))
            return $this->_modules[$moduleId];

        if(($currentController = Yii::app()->getController()) !== null) {
            if(($currentModule = $currentController->getModule()) !== null &&
                $currentModule->id == $moduleId) {
                return ($this->_modules[$moduleId] = $currentModule);
            }
        }

        $modules = Yii::app()->getModules();

        if(!isset($modules[$moduleId]))
            return null;
        if(in_array($moduleId, $this->excludeModules))
            return null;

        $moduleConfig = $modules[$moduleId];

        // Using this setting to safely load modules without change anything global
        YzBaseWebModule::$safeInit = true;

        if(!isset($moduleConfig['enabled']) || $moduleConfig['enabled'])
        {
            $class=$moduleConfig['class'];
            unset($moduleConfig['class'], $moduleConfig['enabled']);

            /** @var $module CWebModule */
            $module = Yii::createComponent($class,$moduleId,null,$moduleConfig);

            if(!($module instanceof YzWebModule)) {
                $module = null;
            }
        }
        else
            $module = null;

        YzBaseWebModule::$safeInit = false;

        if($module !== null)
            $this->_modules[$moduleId] = $module;

        return $module;
    }

    /**
     * @param \YzExtensionsLinker $extensionsLinker
     */
    public function setExtensionsLinker($extensionsLinker)
    {
        $this->_extensionsLinker = $extensionsLinker;
    }

    /**
     * @return \YzExtensionsLinker
     */
    public function getExtensionsLinker()
    {
        if(is_array($this->_extensionsLinker)) {
            $this->_extensionsLinker = Yii::createComponent(array_merge(
                $this->_extensionsLinker, array(
                    'class' => 'YzExtensionsLinker',
                )
            ));
        }
        return $this->_extensionsLinker;
    }

    /**
     * @return boolean
     */
    public function getIsConsoleMode()
    {
        if($this->_isConsoleMode === null) {
            $this->_isConsoleMode = Yii::app() instanceof CConsoleApplication;
        }

        return $this->_isConsoleMode;
    }

    /**
     * @param boolean $alwaysUpdateAssets
     */
    public function setAlwaysUpdateAssets($alwaysUpdateAssets)
    {
        $this->_alwaysUpdateAssets = $alwaysUpdateAssets;
    }

    /**
     * @return boolean
     */
    public function getAlwaysUpdateAssets()
    {
        if($this->_alwaysUpdateAssets === null)
            return defined('YII_DEBUG');
        return $this->_alwaysUpdateAssets;
    }

    /**
     * This method registers bootstrap extension. It should be called
     * from places, where bootstrap is needed to be loaded.
     * @param string $component
     * @return Bootstrap
     */
    public function registerBootstrap($component = 'bootstrap')
    {
        // Import for bootstrap
        Yii::setPathOfAlias($component,
            Yii::getPathOfAlias(Yz::gep('bootstrap')));

        if(!Yii::app()->hasComponent($component)) {
            Yii::app()->setComponent($component, Yii::createComponent(
                Yz::gep('bootstrap').'.components.Bootstrap'
            ));
        } else
            Yii::app()->{$component};

        return Yii::app()->{$component};
    }

    public function registerBootstrapAssets($component = 'bootstrap')
    {
        $this->registerBootstrap($component);

        /** @var $bootstrap Bootstrap */
        $bootstrap = Yii::app()->{$component};
        $bootstrap->registerAllCss();
        $bootstrap->registerCoreScripts();
    }

    public function registerFontAwesome($asset = 'admin.assets.font-awesome')
    {
        $url = Yii::app()->assetManager->publish(Yii::getPathOfAlias($asset), false, -1, Yz::get()->alwaysUpdateAssets);

        /** @var $cs CClientScript */
        $cs = Yii::app()->clientScript;
        if(defined('YII_DEBUG'))
            $cs->registerCssFile($url.'/css/font-awesome.css');
        else
            $cs->registerCssFile($url.'/css/font-awesome.min.css');
    }
}