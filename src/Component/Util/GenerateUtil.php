<?php
namespace App\Component\Util;

/**
 * Generate Util
 * 
 * Basic Generate Util
 */
class GenerateUtil
{
    /**
     * Generate Unique Number
     * 
     * @param string length
     */
    public static function number($length)
    {
        $charset = '0123456789';
        $arrange = strlen($charset) - 1;
        $value = '';
        for ($i = 0; $i < $length; ++$i) {
            $format = rand(0, $arrange);
            $value .= $charset[$format];
        }

        // Remove zerro on starting
        $removeZero = ('0' === substr($value, 0, 1)) ? '1'.substr($value, 1) : $value;

        // Return Response
        return $removeZero;
    }
}