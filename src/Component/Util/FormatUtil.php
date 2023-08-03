<?php
namespace App\Component\Util;

use DateTime;

/**
 * Format Util
 * 
 * Anything util handle here
 */
class FormatUtil
{
    /**
     * Phone Number
     * 
     * @param string dialCode
     * @param string phone
     * 
     */
    public static function phoneNumber(
        string $dialCode,
        string $phone
    )
    {
        // Format phone number
        $phoneNumber = ltrim($phone, '+');

        // Get dial code length
        $dialCodeLength = (int) strlen($dialCode);

        // Remove dial code at start
        $phoneNumber = (substr($phoneNumber, 0, $dialCodeLength) == $dialCode) ? substr($phoneNumber, $dialCodeLength) : $phoneNumber;

        // Remove zero at start
        $phoneNumber = (substr($phoneNumber, 0, 1) == 0) ? substr($phoneNumber, 1) : $phoneNumber;

        // Return Clean Phone
        return $phoneNumber;
    }

    /**
     * Date to Datetime
     * 
     * @param string date
     * @return Datetime
     * 
     */
    public static function dateToDateTime(
        string $date,
        string $format = 'd/m/Y'
    )
    {
        // Convert to datetime
        $datetime = \DateTime::createFromFormat($format, $date);

        // Return Datetime
        return $datetime;
    }
}