<?php
namespace App\Component\Util;

/**
 * Response Util
 * 
 * Format response
 */
class ResponseUtil
{
    /**
     * Json Response
     * 
     * @param int status
     * @param array data
     * @param string message
     * @return array
     * 
     */
    public static function jsonResponse(
        int $status,
        array $data = null,
        string $message = null
    )
    {
        // Hold Response
        $response = [
            'status' => $status,
            'data' => $data,
            'message' => $message
        ];

        // Return Response
        return $response;
    }

    /**
     * Response
     * 
     * Format response according to jsonResponse
     * if value is true return array else return class or strings
     * 
     * @param bool jsonResponse
     * @param mixed dafault
     * @param int status
     * @param array data
     * @param string message
     * 
     */
    public static function response(
        bool $jsonResponse,
        $default = null,
        int $status = null,
        array $data = null,
        string $message = null
    )
    {
        // Hold Response
        $response = null;

        // Switch case
        switch ($jsonResponse) {
            case true:
                // Format json
                $response = self::jsonResponse($status, $data, $message);
                break;
            
            default:
                # Default
                $response = $default;
                break;
        }

        // Return Response
        return $response;
    }
}