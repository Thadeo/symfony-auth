<?php
namespace App\EventListener;

use App\Component\Util\ResponseUtil;
use App\Service\AuthService;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationFailureEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Response\JWTAuthenticationFailureResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ApiJWTEventListener
{
    private $session;
    
    public function __construct(
        private RequestStack $requestStack,
        private TranslatorInterface $lang,
        private AuthService $auth,
        private TokenStorageInterface $tokenStorage
    )
    {
        $this->lang = $lang;
        $this->auth = $auth;
        $this->tokenStorage = $tokenStorage;

        // We use request stack to get session because - because 
        // SessionInterface it cause issue when we access in controller
        $this->session = $requestStack->getSession();
    }

    /**
     * @param AuthenticationSuccessEvent $event
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        $user = $event->getUser();

        if (!$user instanceof UserInterface) {
            return;
        }

        // Check if Login Authentication Required
        if($user->isAuthFactorLogin()) {

            // Get available authenticator
            $auth = $this->auth->factorAllTypeAuth($user, 'auth_login');

            // Set user in session
            $this->session->set('apiUser', $user->getEmail());

            // Add Event Data
            $event->setData($auth);
            
        }else {

            // Successful Response Data
            $response['2factor'] = false;
            $response['type'] = 'Bearer';
            $response['token'] = $data['token'];

            $event->setData(ResponseUtil::jsonResponse(200, $response, $this->lang->trans('auth.api.success')));
        }
    }

    /**
     * @param AuthenticationFailureEvent $event
     */
    public function onAuthenticationFailureResponse(AuthenticationFailureEvent $event)
    {
        $response = new JWTAuthenticationFailureResponse($event->getException()->getMessage());
        
        // Change Response Data
        $response->setJson(json_encode(ResponseUtil::jsonResponse(401, null, $event->getException()->getMessage())));

        $event->setResponse($response);
    }
}