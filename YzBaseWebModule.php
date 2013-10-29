<?php

/**
 * This class lets us load Yz Modules in {@link YzComponent::getModules} function
 * with safe initialization with standard {@link CWebModule::init} function.
 * This is needed to prevent module of redefining some of the system variables
 * (such as {@link CWebApplication::user}, etc).<br>
 */
class YzBaseWebModule extends CWebModule
{
    /**
     * This property indicates that all children of this class
     * should initialize safely
     * @var bool
     */
    public static $safeInit = false;
}