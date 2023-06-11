<?php

namespace App\Controller\Api;

use App\Component\Request\AppRequest;
use App\Service\AuthService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    /**
     * Auth Register
     * 
     * User Registration
     */
    #[Route('/api/auth/register', name: 'api_auth_register', methods: ['POST'])]
    public function register(
        AppRequest $request,
        AuthService $auth
    ): Response
    {
        $validate = $request->validate([
            'country_code' => 'required|string',
            'first_name' => 'required|string',
            'middle_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        // Verify Validation
        if(!empty($validate['errors'])) return $this->json($validate, 400);;
        
        // Register User
        $register = $auth->registerUser(true,
                        $validate['country_code'],
                        $validate['first_name'],
                        $validate['middle_name'],
                        $validate['last_name'],
                        $validate['email'],
                        $validate['password']);
        // Return Response
        return new Response(json_encode($register), Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    /**
     * 2-Factor Authentication
     * 
     * List all auth type such as
     * email, sms and e.t.c
     */
    #[Route('/api/auth/factor', name: 'api_auth_factor', methods: ['POST'])]
    public function factor(
        AppRequest $request,
        AuthService $auth
    ): Response
    {
        $user = $this->getUser();

        // Validate Rules
        $validate = $request->validate([
            'auth' => 'required|string'
        ]);

        // Verify Validation
        if(!empty($validate['errors'])) return $this->json($validate, 400);

        // Auth
        $authUser = $auth->factorAllTypeAuth($user, $validate['auth']);

        // Return Response
        return $this->json($authUser, $authUser['status']);
    }

    /**
     * 2-Factor Submit
     * 
     * Submit data for authenticate 
     */
    #[Route('/api/auth/factor/submit', name: 'api_auth_factor_submit', methods: ['POST'])]
    public function factorAuthSubmit(
        AppRequest $request,
        AuthService $auth
    ): Response
    {
        $user = $this->getUser();

        // Validate Rules
        $validate = $request->validate([
            'auth_type' => 'required|string'
        ]);

        // Verify Validation
        if(!empty($validate['errors'])) return $this->json($validate, 400);
        
        // Auth
        $authUser = $auth->factorAuthSubmit($user, $validate['auth_type']);

        // Return Response
        return $this->json($authUser, $authUser['status']);
    }

    /**
     * 2-Factor Confirm
     * 
     * Confirm Authentication submitted
     */
    #[Route('/api/auth/factor/confirm', name: 'api_auth_factor_confirm', methods: ['POST'])]
    public function factorAuthConfirm(
        AppRequest $request,
        AuthService $auth
    ): Response
    {
        $user = $this->getUser();

        // Validate Rules
        $validate = $request->validate([
            'auth_type' => 'required|string',
            'token' => 'required|string'
        ]);

        // Verify Validation
        if(!empty($validate['errors'])) return $this->json($validate, 400);
        
        // Auth
        $authUser = $auth->factorAuthConfirmSubmit($user, $validate['auth_type'], $validate['token']);

        // Return Response
        return $this->json($authUser, $authUser['status']);
    }
}
