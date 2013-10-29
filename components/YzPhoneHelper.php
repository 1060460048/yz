<?php

class YzPhoneHelper
{
    /**
     * @param string $phone
     * @return string
     */
    public static function normalizePhone($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        $phone = preg_replace('/^(7|8)(\d{10})$/', '$2', $phone);
        return $phone;
    }

    /**
     * @param string $phone
     * @return string
     */
    public static function formatPhone($phone)
    {
        return preg_replace('/^(\d{3})(\d{3})(\d{2})(\d{2})$/', '+7 ($1) $2-$3-$4', $phone);
    }

    public static function callablePhone($phone)
    {
        return '+7'.self::normalizePhone($phone);
    }
}