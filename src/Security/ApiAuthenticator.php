<?php
namespace App\Security;

use App\Component\Util\ResponseUtil;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\AuthorizationHeaderTokenExtractor;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;

/**
 * Extends API Authenticator
 */
class ApiAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private JWTTokenManagerInterface $jwttoken
    )
    {
        $this->jwttoken = $jwttoken;
    }

    public function supports(Request $request): ?bool
    {
        return $request->headers->has('Authorization');
    }

    public function authenticate(Request $request): Passport
    {
        $apiToken = $request->headers->get('Authorization');

        $isJwtToken = false;

        if (null === $apiToken) {
            // The token header was empty, authentication fails with HTTP Status
            // Code 401 "Unauthorized"
            throw new CustomUserMessageAuthenticationException('No API token provided');
        }

        // Verify if is JWT token
        if('Bearer' == substr($apiToken, 0, 6)) {

            $isJwtToken = true;

            try {
                
                $extractor = new AuthorizationHeaderTokenExtractor('Bearer', 'Authorization');

                $apiToken = $extractor->extract($request);

                if(!$apiToken) throw new AuthenticationException("Unable to verify the token");
                
                // Get JWT Data
                $jwtData = $this->jwttoken->parse($apiToken);

                // Get Login Details
                $username = $jwtData['email'];

            } catch (JWTDecodeFailureException $th) {
                //throw $th;
                throw new CustomUserMessageAuthenticationException('Token invalid or expired', [], 401);

            } catch (AuthenticationException $th) {
                //throw $th;
                throw new CustomUserMessageAuthenticationException($th->getMessage());
            }
            
        }

        return new SelfValidatingPassport(new UserBadge($username));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        // Hold default http code
        $httpcode = Response::HTTP_BAD_REQUEST;

        // Unauthorized
        if($exception->getCode() == 401) $httpcode = Response::HTTP_UNAUTHORIZED;

        // Response
        $response = ResponseUtil::jsonResponse($httpcode, null, strtr($exception->getMessageKey(), $exception->getMessageData()));
        
        return new JsonResponse($response, $response['status']);
    }
}