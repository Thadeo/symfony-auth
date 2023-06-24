<?php
namespace App\Component\Util;

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
}