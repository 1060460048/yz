<?php
/**
 * YzSettingsComponent class file. 
 */

class YzSettingsComponent extends CApplicationComponent
{
    /**
     * @var string
     */
    public $defaultSettingGroupName='default';
    /**
     * @var integer
     */
    public $cacheExpirationTime = null;
    /**
     * @var bool
     */
    public $loadAllGroups = false;

    /**
     * @var string
     */
    protected $cachePrefix = __CLASS__;
    /**
     * @var array
     */
    protected static $loadedGroups = array();
    /**
	 * Initializes the application component.
	 * This method is required by {@link IApplicationComponent} and is invoked by application.
	 * If you override this method, make sure to call the parent implementation
	 * so that the application component can be marked as initialized.
	 */
	public function init()
	{
		parent::init();

        $this->cacheExpirationTime = is_null($this->cacheExpirationTime) ?
            Yii::app()->params['yzCacheExpirationTime'] : $this->cacheExpirationTime;
	}

    /**
     * @param $settingName string|array
     * @return bool
     */
    public function exits($settingName)
    {
        $settingName = $this->normalizeSettingName($settingName);
        $group = $this->loadGroup($settingName[0]);
        if(is_null($group) || !isset($group[$settingName[1]]))
            return false;
        else
            return true;
    }

    /**
     * @param $settingName string|array
     * @return mixed
     */
    public function get($settingName)
    {
        $settingName = $this->normalizeSettingName($settingName);
        $group = $this->loadGroup($settingName[0]);
        if(is_null($group) || !isset($group[$settingName[1]]))
            return null;
        else
            return $group[$settingName[1]];
    }

    /**
     * @param $settingName string|array
     * @param $value mixed
     * @return boolean
     */
    public function set($settingName, $value)
    {
        $settingName = $this->normalizeSettingName($settingName);

        /** @var $groupSetting YzSettingsGroups */
        $groupSetting = YzSettingsGroups::model()->with(array(
            'setting' => array(
                'condition' => 'setting.name = :name',
                'params' => array(':name'=>$settingName[1]),
            )
        ))->find(array(
            'name' => $settingName[0],
        ));
        if(empty($groupSetting))
            return false;
        if(empty($groupSetting->settings))
            return false;

        /** @var $setting YzSettings */
        $setting = $groupSetting->settings[0];

        $setting->value = $value;

        if($setting->save()) {
            // TODO Maybe not delete cache, but change it value
            self::$loadedGroups = array();
            Yii::app()->cache->delete($this->getCacheId());
            return true;
        } else
            return false;
    }

    /**
     * @param $name string|array
     * @return array
     */
    protected function normalizeSettingName($name)
    {
        if(is_array($name))
            return array($name[0],$name[1]);
        else {
            $settingName = trim($name,'. ');
            if(strpos($settingName,'.'))
                list($groupName, $settingName) = explode('.', $name, 2);
            else {
                $groupName = $this->defaultSettingGroupName;
                $settingName = $name;
            }
            return array($groupName, $settingName);
        }
    }

    /**
     * @param $groupName string
     * @return null|string|int|float
     */
    protected function loadGroup($groupName)
    {
        if(isset(self::$loadedGroups[$groupName]))
            return self::$loadedGroups[$groupName];

        self::$loadedGroups = Yii::app()->cache->get($this->getCacheId());

        if(self::$loadedGroups === false || !isset(self::$loadedGroups[$groupName])){
            if($this->loadAllGroups)
                $groups = YzSettingsGroups::model()->with('settings')->findAll();
            else
                $groups = array(YzSettingsGroups::model()->with('settings')->find(array(
                    'name' => $groupName,
                )));
            foreach( $groups as $group ) {
                if( empty($group) )
                    continue;
                foreach( $group->settings as $setting ) {
                    self::$loadedGroups[$group->name][$setting->name] = $setting->value;
                }
            }
            Yii::app()->cache->set($this->getCacheId(),self::$loadedGroups, $this->cacheExpirationTime);
        }

        return isset(self::$loadedGroups[$groupName]) ? self::$loadedGroups[$groupName] : null;
    }

    /**
     * @return string
     */
    protected function getCacheId()
    {
        return $this->cachePrefix . '_Cache';
    }
}
