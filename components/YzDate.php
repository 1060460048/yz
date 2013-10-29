<?php

class YzDate extends YzDateTime
{
    public static function init($datetime, $format = null)
    {
        return new YzDate($datetime, $format);
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
                return strftime('%Y-%m-%d', $timestamp);

            case self::STRING:
            case self::I18N:
                return Yii::app()->dateFormatter->formatDateTime($timestamp,'medium',null);

        }
    }

    public function equal($date)
    {
        return strtotime('today', $this->_timestamp) == strtotime('today',$date->timestamp);
    }

    public function later($date)
    {
        return strtotime('today', $this->_timestamp) - strtotime('today',$date->timestamp) > 0;
    }

    public function earlier($date)
    {
        return strtotime('today', $this->_timestamp) - strtotime('today',$date->timestamp) < 0;
    }
}