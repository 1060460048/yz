<?php
/**
 * This file is linker for proper version of YzEvents.
 */

if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
    include_once(dirname(__FILE__) . '/YzEvents/YzEvents_5.3.php');
} else {
    include_once(dirname(__FILE__) . '/YzEvents/YzEvents_5.2.php');
}