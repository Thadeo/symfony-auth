<?php
namespace App\Component\Request;

use App\Component\Validation\AppValidation;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Basic App Request
 * 
 * Handle Request
 * Handle Validation
 */
class AppRequest
{
    private $request;
    private $validation;

    public function __construct(
        RequestStack $request,
        AppValidation $validation
    )
    {
        $this->request = $request;
        $this->validation = $validation;
    }

    /**
     * Validate
     * 
     * handle validation for form & json requested post
     * @param array rules
     */
    public function validate(array $rules): array
    {
        $content = json_decode($this->getContent(), true);

        $validate = $this->validation->validate($content, $rules);

        return $validate;
    }

    /**
     * Get Current Request
     */
    public function request()
    {
        return $this->request->getCurrentRequest();
    }

    /**
     * Get Content
     */
    public function getContent()
    {
        return $this->request()->getContent();
    }

    /**
     * Get Session
     */
    public function getSession()
    {
        return $this->request()->getSession();
    }
}