<?php

class YzPasshashBehavior extends CActiveRecordBehavior
{
    public $passwordField = 'password';
    public $passhashField = 'passhash';
    public $saltField = 'salt';

    public $saltLength = 16;

    public $on = 'insert,changePassword';

    public $hashMethod = array(__CLASS__, 'hash');

    public function beforeSave($event)
    {
        if(!is_array($this->on))
            $this->on = preg_split('/[ ,]+/',$this->on);

        if(empty($this->on) || in_array($this->owner->scenario, $this->on))
            $this->hashPassword();
    }


    protected function hashPassword()
    {
        $owner = $this->getOwner();

        $salt = $this->generateSalt();
        $password = $owner->{$this->passwordField};

        $passhash = call_user_func($this->hashMethod, $password, $salt);

        $owner->{$this->passhashField} = $passhash;
        $owner->{$this->saltField} = $salt;
    }

    protected function generateSalt()
    {
        $alphabet = 'abcdefghijklmopqrstuvwxyz0123456789!@#$%^&*()<>?~';

        $salt = '';

        for($i = 0; $i < $this->saltLength; $i++){
            $salt .= $alphabet[mt_rand(0,strlen($alphabet)-1)];
        }

        return $salt;
    }

    public static function hash($password, $salt)
    {
        return md5(md5($password) . $salt);
    }
}