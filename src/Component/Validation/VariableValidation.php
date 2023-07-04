<?php
namespace App\Component\Validation;

/**
 * Variable Validation
 */
class VariableValidation
{

    /**
     * Clean input date
     */
    public static function cleanInputData($data)
    {
        // Hold clean
        $cleanValue = $data;

        // Verify is numeric, amount & phone
        if(self::isNumeric($data) || self::isAmount($data)) {

            // Clean
            $cleanValue = filter_var($data, FILTER_SANITIZE_NUMBER_INT);

            // Change to int
            $cleanValue = (int) $cleanValue;
        }

        // Verify is string
        if(self::isString($data)) $cleanValue = htmlspecialchars($data);

        // Verify is phone
        if(self::isPhone($data)) $cleanValue = filter_var($data, FILTER_SANITIZE_NUMBER_INT);

        // Verify is email
        if(self::isEmail($data)) $cleanValue = filter_var($data, FILTER_SANITIZE_EMAIL);

        // Verify is url
        if(self::isUrl($data)) $cleanValue = filter_var($data, FILTER_SANITIZE_URL);

        // Return Data
        return $cleanValue;
    }

    /**
     * Convert string to array
     */
    public static function convertStringToArray($separate, $string): array
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
    public static function isArrayOrString($data): string
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
     * 
     * @param string value
     * @param string max
     */
    public static function isMin($value, $min): bool
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
     * 
     * @param string value
     * @param string max
     */
    public static function isMax($value, $max): bool
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
     * 
     * @param string string
     */
    public static function isString($string): bool
    {
        // Verify if is string
        if(!is_string($string)) return false;

        // Return Response
        return true;
    }

    /**
     * Numeric
     * 
     * @param string numeric
     */
    public static function isNumeric($numeric): bool
    {
        // Verify if is numeric
        if(!preg_match('/^\d+$/', $numeric)) return false;

        // Return Response
        return true;
    }

    /**
     * Amount
     * 
     * @param string amount
     */
    public static function isAmount($amount): bool
    {
        // Verify if is numeric
        if(!preg_match('/^\d+(\.\d+)?$/', $amount)) return false;

        // Return Response
        return true;
    }

    /**
     * Boolean
     * 
     * @param string bool
     */
    public static function isBool($bool): bool
    {
        // Verify if is bool
        if(!is_bool($bool)) false;

        // Return Response
        return true;
    }

    /**
     * Phone
     * 
     * @param string phone
     */
    public static function isPhone($phone): bool
    {
        // Verify if is phone
        if(!filter_var($phone, FILTER_VALIDATE_INT)) return false;

        // Return Response
        return true;
    }

    /**
     * Email Address
     * 
     * @param string email
     */
    public static function isEmail($email): bool
    {
        // Verify if is email
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) return false;

        // Return Response
        return true;
    }

    /**
     * Url
     * 
     * @param string url
     */
    public static function isUrl($url): bool
    {
        // Verify if is url
        if(!filter_var($url, FILTER_VALIDATE_URL)) return false;

        // Return Response
        return true;
    }

    /**
     * Match
     * 
     * @param string match
     */
    /*public static function isMatch($match): bool
    {
        // Verify if string or array
        $verifyType = self::isArrayOrString($match);

        // Verify type is null
        if($verifyType == null) return false;

        // Check if is array
        if($verifyType == "array") {
            // Verify if not empty
            if(empty($match)) return false;
        }

        // Check if is string
        if($verifyType == "string") {
            // Verify if not empty
            if(empty($match)) return false;

            // Replace
            $replace = str_replace(['[', ']'], '', $match);

            // Change to array
            $array = explode(',', $replace);

            // Verify if is array
            if(!is_array($array)) return false;
        }

        // Return Response
        return true;
    }*/

    /**
     * Match
     * 
     * @param string value
     * @param string match
     */
    public static function isMatch($value, $match): bool
    {
        // Verify if string or array
        $verifyType = self::isArrayOrString($match);

        // Verify type is null
        if($verifyType == null) return false;

        // Check if is array
        if($verifyType == "array") {
            // Verify if not empty
            if(empty($match)) return false;

            // Match
            if(!in_array($value, $match)) return false;
        }

        // Check if is string
        if($verifyType == "string") {
            
            // Verify if not empty
            if(empty($match)) return false;

            // Replace
            $replace = str_replace(['[', ']'], '', $match);

            // Change to array
            $array = explode(',', $replace);

            // Verify if is array
            if(!is_array($array)) return false;

            // Match
            if(!in_array($value, $array)) return false;
        }

        // Return Response
        return true;
    }
}