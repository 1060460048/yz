<?php

/**
 * Class YzEventHandler
 */
abstract class YzEventHandler extends CComponent
{
    /**
     * Main method of handler.
     * @abstract
     * @param $event CEvent
     * @return boolean
     */
    abstract public function handle($event);
}