<?php
namespace App\Service;

use App\Component\Util\ResponseUtil;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class AuthService
{
    private $tokenStorage;
    private $session;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        SessionInterface $session
    )
    {
        $this->tokenStorage = $tokenStorage;
        $this->session = $session;
    }

    /**
     * User Authentication
     * 
     * This is used to authenticate
     * user and can't be uses for api just for form login
     * 
     * @param bool jsonResponse
     * @param User user
     */
    public function userAuthenticate(
        bool $jsonResponse,
        User $user
    )
    {
        try {
            // Get Token
            $token = new UsernamePasswordToken($user, 'main', $user->getRoles());

            // Add Token to Session
            $this->tokenStorage->setToken($token);

            // Add Security Token
            $this->session->set('_security_main', serialize($token));

            // Return User
            return ResponseUtil::response($jsonResponse, $user, 200, ['user' => $user->getFullName(), 'email' => $user->getEmail()], 'Authentication Successful');

        } catch (\Exception $th) {
            //throw $th;
            return ResponseUtil::response($jsonResponse, $user, 400, null, 'Authentication Failed '.$th->getMessage());
        }
    }
}