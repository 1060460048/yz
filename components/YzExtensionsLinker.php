<?php

/**
 * YzExtensionsLinker class gives ability to easily change destination
 * of any third-party extensions, used by Yz or YzExt.
 *
 * @property array $extensions Path to known extensions
 */
class YzExtensionsLinker extends CComponent
{
    protected $_extensions = array(
        'YzExt' => 'ext.YzExt',
        'YiiExt' => 'ext.yiiext',
        'bootstrap' => 'ext.bootstrap',
    );

    /**
     * @param null|array $extensions
     */
    public function setExtensions($extensions)
    {
        if(is_array($extensions))
            $this->_extensions = array_merge($this->_extensions, $extensions);
        elseif($extensions === null)
            $this->_extensions = array();
    }

    /**
     * @return array
     */
    public function getExtensions()
    {
        return $this->_extensions;
    }

    /**
     * Returns path to extension. If path is not set, then default value will be used
     * @param string $name
     * @param string $default
     * @return string
     */
    public function getExtensionPath($name, $default = null)
    {
        if(isset($this->_extensions[$name]))
            return $this->_extensions[$name];
        else
            return $default;
    }
}