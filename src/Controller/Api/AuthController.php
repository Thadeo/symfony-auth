<?php

namespace App\Controller\Api;

use App\Component\Request\AppRequest;
use App\Component\Util\EntityUtil;
use App\Component\Util\ResponseUtil;
use App\Service\AuthService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

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
     * Token 2-Factor Submit
     * 
     * Submit data for authenticate 
     */
    #[Route('/api/auth/token/factor/submit', name: 'api_auth_login_factor_submit', methods: ['POST'])]
    public function factorAuthSubmit(
        AppRequest $request,
        AuthService $auth,
        TranslatorInterface $lang
    ): Response
    {
        // Check if user available in session
        if($request->getSession()->get('apiUser') == null) return $this->json(ResponseUtil::jsonResponse(401, null, $lang->trans('auth.api.unauthorized')), 401);

        // Validate Rules
        $validate = $request->validate([
            'identifier' => 'required|string'
        ]);

        // Verify Validation
        if(!empty($validate['errors'])) return $this->json($validate, 400);
        
        // Auth
        $authUser = $auth->factorAuthSubmit(true, $request->getSession()->get('apiUser'), 'auth_login', $validate['identifier']);

        // Return Response
        return $this->json($authUser, $authUser['status']);
    }

    /**
     * Token 2-Factor Confirm
     * 
     * Confirm Authentication submitted
     */
    #[Route('/api/auth/token/factor/confirm', name: 'api_auth_login_factor_confirm', methods: ['POST'])]
    public function factorAuthConfirm(
        AppRequest $request,
        AuthService $auth,
        TranslatorInterface $lang
    ): Response
    {
        // Check if user available in session
        if($request->getSession()->get('apiUser') == null) return $this->json(ResponseUtil::jsonResponse(401, null, $lang->trans('auth.api.unauthorized')), 401);

        // Validate Rules
        $validate = $request->validate([
            'identifier' => 'required|string',
            'token' => 'required|string'
        ]);

        // Verify Validation
        if(!empty($validate['errors'])) return $this->json($validate, 400);
        
        // Allow App to Authenticate
        // This is option if you plan to use in web form, system should session and no api token return
        $isApp = (isset($validate['isApp'])) ? true : false;

        // Auth
        $authUser = $auth->userLoginFactorConfirm(true, $request->getSession()->get('apiUser'), $isApp, $validate['identifier'], $validate['token']);

        // Return Response
        return $this->json($authUser, $authUser['status']);
    }

    /**
     * Forget Password
     * 
     * Submit forget
     */
    #[Route('/api/auth/forget/password', name: 'api_auth_forget_password', methods: ['POST'])]
    public function forgetPassword(
        AppRequest $request,
        AuthService $auth,
        TranslatorInterface $lang
    ): Response
    {

        // Check if user available in session
        if($request->getSession()->get('apiUser') == null) return $this->json(ResponseUtil::jsonResponse(401, null, $lang->trans('auth.api.unauthorized')), 401);

        // Validate Rules
        $validate = $request->validate([
            'identifier' => 'required|string',
            'token' => 'required|string',
            'password' => 'required|string'
        ]);

        // Verify Validation
        if(!empty($validate['errors'])) return $this->json($validate, 400);

        // Auth
        $authUser = $auth->forgetUserPassword(true, $request->getSession()->get('apiUser'), $validate['identifier'], $validate['token'], $validate['password']);

        // Return Response
        return $this->json($authUser, $authUser['status']);
    }

    /**
     * Password Forget 2-Factor
     * 
     * List all auth type such as
     * email, sms and e.t.c
     */
    #[Route('/api/auth/forget/password/factor', name: 'api_auth_forget_password_factor', methods: ['POST'])]
    public function forgetPasswordFactor(
        AppRequest $request,
        AuthService $auth,
        EntityManagerInterface $entityManager,
        TranslatorInterface $lang
    ): Response
    {

        // Validate Rules
        $validate = $request->validate([
            'email' => 'required|email'
        ]);

        // Verify Validation
        if(!empty($validate['errors'])) return $this->json($validate, 400);

        // Find User
        $user = EntityUtil::findOneUser($lang, $entityManager, $validate['email']);

        // User not found
        if($user instanceof \Exception) return $this->json(ResponseUtil::jsonResponse(401, null, $user->getMessage()), 401);
        
        // Set User in session
        $request->getSession()->set('apiUser', $user);

        // Auth
        $authUser = $auth->factorAllTypeAuth($user, 'auth_reset_password');

        // Return Response
        return $this->json($authUser, $authUser['status']);
    }

    /**
     * Forget Password 2-Factor Submit
     * 
     * Submit data for authenticate 
     */
    #[Route('/api/auth/forget/password/factor/submit', name: 'api_auth_forget_password_factor_submit', methods: ['POST'])]
    public function forgetPasswordFactorSubmit(
        AppRequest $request,
        AuthService $auth,
        TranslatorInterface $lang
    ): Response
    {

        // Check if user available in session
        if($request->getSession()->get('apiUser') == null) return $this->json(ResponseUtil::jsonResponse(401, null, $lang->trans('auth.api.unauthorized')), 401);

        // Validate Rules
        $validate = $request->validate([
            'identifier' => 'required|string'
        ]);

        // Verify Validation
        if(!empty($validate['errors'])) return $this->json($validate, 400);
        
        // Auth
        $authUser = $auth->factorAuthSubmit(true, $request->getSession()->get('apiUser'), 'auth_reset_password', $validate['identifier']);

        // Return Response
        return $this->json($authUser, $authUser['status']);
    }
}
