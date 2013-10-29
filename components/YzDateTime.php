<?php

/**
 * @property int $timestamp 
 * @property string $mysql 
 * @property string $local 
 * @property string $i18n
 */
class YzDateTime extends CComponent
{
    const MYSQL = 'mysql';
    const LOCAL = 'local';
    const I18N = 'i18n';
    const STRING = 'string';
    const TIMESTAMP = 'timestamp';

    /**
     * @var int
     */
    protected $_timestamp = null;

    protected $_classesTable = array(
        'datetime' => 'YzDateTime',
        'date' => 'YzDate',
    );

    public function __construct($datetime = null, $format = null)
    {
        if($datetime === null)
            return;
        $this->_timestamp = $this->toTimestamp($datetime,$format);
    }

    /**
     * @param $datetime
     * @param null $format
     * @return YzDateTime
     */
    public static function init($datetime,$format = null)
    {
        return new YzDateTime($datetime,$format);
    }

    /**
     * Reformat datetime string to another format. See {@see strftime} for available formats
     * @param string $datetime
     * @param string $format
     * @return string
     */
    public static function str2str($datetime, $format)
    {
        return strftime($format, strtotime($datetime));
    }

    /**
     * @param string $type
     * @return YzDateTime
     * @throws CException
     */
    public function to($type)
    {
        if(!isset($this->_classesTable[$type]))
            throw new CException('Type is unknown');

        $class = $this->_classesTable[$type];
        /** @var $obj YzDateTime */
        $obj = new $class;
        $obj->timestamp = $this->_timestamp;
        return $obj;
    }

    /**
     * @param YzDateTime $date
     * @return bool
     */
    public function equal($date)
    {
        return $this->_timestamp == $date->timestamp;
    }

    /**
     * @param YzDateTime $date
     * @return bool
     */
    public function later($date)
    {
        return $this->_timestamp > $date->timestamp;
    }

    /**
     * @param YzDateTime $date
     * @return bool
     */
    public function earlier($date)
    {
        return $this->_timestamp < $date->timestamp;
    }

    /**
     * @return int
     */
    public function getTimestamp()
    {
        return $this->_timestamp;
    }

    /**
     * @param int $timestamp
     */
    public function setTimestamp($timestamp)
    {
        $this->_timestamp = $timestamp;
    }

    /**
     * @return string
     */
    public function getMysql()
    {
        return $this->fromTimestamp($this->_timestamp,self::MYSQL);
    }

    public function setMysql($datetime)
    {
        $this->_timestamp = $this->toTimestamp($datetime,self::MYSQL);
    }

    /**
     * @return string
     */
    public function getI18n()
    {
        return $this->fromTimestamp($this->_timestamp,self::I18N);
    }

    public function setI18n($datetime)
    {
        $this->_timestamp = $this->toTimestamp($datetime,self::I18N);
    }

    /**
     * @return string
     */
    public function getLocal()
    {
        return $this->fromTimestamp($this->_timestamp,self::I18N);
    }

    public function setLocal($datetime)
    {
        $this->_timestamp = $this->toTimestamp($datetime,self::I18N);
    }

    public function toFormat($format)
    {
        return strftime($format, $this->_timestamp);
    }

    /**
     * @return string
     */
    function __toString()
    {
        return $this->fromTimestamp($this->_timestamp,self::STRING);
    }


    /**
     * @param string $datetime
     * @param string $format
     * @return int
     * @throws CException
     */
    protected function toTimestamp($datetime, $format = null)
    {
        switch(true) {
            default:
                throw new CException('Datetime has wrong format');
                break;

            case ($datetime instanceof YzDateTime):
                $this->_timestamp = $datetime->timestamp;
                break;

            case $format === self::TIMESTAMP:
            case is_null($format) && gettype($datetime) == 'integer':
                return $datetime;
            case $format === self::MYSQL:
            case $format === self::I18N:
            case $format === self::LOCAL:
            case $format === self::STRING:
            case is_null($format) && gettype($datetime) == self::STRING:
                return strtotime($datetime);
        }
    }

    /**
     * @param int $timestamp
     * @param string $format
     * @return string
     * @throws CException
     */
    protected function fromTimestamp($timestamp, $format = self::MYSQL)
    {
        switch($format) {
            default:
                throw new CException('Format is unknown');
                break;

            case self::MYSQL:
                return strftime('%Y-%m-%d %H:%M:%S', $timestamp);

            case self::STRING:
            case self::I18N:
                return Yii::app()->dateFormatter->formatDateTime($timestamp,'medium','medium');
        }
    }
}