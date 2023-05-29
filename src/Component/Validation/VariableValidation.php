<?php
namespace App\Component\Validation;

/**
 * Variable Validation
 */
class VariableValidation
{

    /**
     * Convert string to array
     */
    public static function convertStringToArray($separate, $string)
    {
        // Default value
        $response = null;

        // Verify if is string or array
        if(self::isArrayOrString($string) == 'array') return $string;

        // Convert string to array
        $array = (empty($string)) ? null : explode($separate, $string);

        // Verify if is array
        if(is_array($array)) $response = $array;

        // Return Response
        return $response;
    }

    /**
     * Verify value if is array or string
     */
    public static function isArrayOrString($data)
    {
        // Default value
        $response = null;

        // Check Array
        if(is_array($data)) {
            # array
            $response = 'array';
        }elseif (is_string($data)) {
            # string
            $response = 'string';
        }

        // Return Response
        return $response;
    }

    /**
     * Min
     */
    public static function isMin($value, $min)
    {
        // Verify if is numeric
        if(!self::isNumeric($value) && !self::isString($value)) return false;

        // Get number
        $value = (self::isNumeric($value)) ? (int) $value : (int) strlen(trim($value));

        // Check less than
        if($value < $min) return false;

        // Return Response
        return true;
    }

    /**
     * Max
     */
    public static function isMax($value, $max)
    {
        // Verify if is numeric
        if(!self::isNumeric($value) && !self::isString($value)) return false;

        // Get number
        $value = (self::isNumeric($value)) ? (int) $value : (int) strlen(trim($value));

        // Check greater than
        if($value > $max) return false;

        // Return Response
        return true;
    }

    /**
     * String
     */
    public static function isString($string)
    {
        // Verify if is string
        if(!is_string($string)) return false;

        // Return Response
        return true;
    }

    /**
     * Numeric
     */
    public static function isNumeric($numeric)
    {
        // Verify if is numeric
        if(!preg_match('/^\d+$/', $numeric)) return false;

        // Return Response
        return true;
    }

    /**
     * Amount
     */
    public static function isAmount($amount)
    {
        // Verify if is numeric
        if(!preg_match('/^\d+(\.\d+)?$/', $amount)) return false;

        // Return Response
        return true;
    }

    /**
     * Email Address
     */
    public static function isEmailAddress($email)
    {
        // Verify if is email
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) return false;

        // Return Response
        return true;
    }
}