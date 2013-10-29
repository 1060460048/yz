<?php

class YzIPFieldBehavior extends CActiveRecordBehavior
{
    public $IPField = 'ip';
    public $IPBinaryField = 'ipbin';

    public function beforeSave($event)
    {
        $this->owner->{$this->IPBinaryField} =
            $this->ip2bin($this->owner->{$this->IPField});
    }

    public function afterFind()
    {
        $this->owner->{$this->IPField} =
            $this->bin2ip($this->owner->{$this->IPBinaryField});
    }

    protected function ip2bin($ip)
    {
        return inet_pton($ip);
    }

    protected function bin2ip($bin)
    {
        return inet_ntop($bin);
    }
}