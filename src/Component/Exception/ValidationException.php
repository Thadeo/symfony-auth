<?php
namespace App\Component\Exception;

use Exception;

/**
 * Validation Exception
 */
class ValidationException extends Exception
{
    private $data;

    public function __construct($message = "", $data = [], $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->addData($data);
    }

    /**
     * Add Data
     * 
     * @return array
     */
    public function addData($data)
    {
        // Hold Data
        $this->data = ['errors' => $data, 'message' => parent::getMessage()];
    }

    /**
     * Get Data
     * 
     * @return array
     */
    public function getData()
    {
        // Return Array
        return $this->data;
    }
}