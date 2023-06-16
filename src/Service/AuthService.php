<?php
namespace App\Service;

use App\Component\Util\EntityUtil;
use App\Component\Util\GenerateUtil;
use App\Component\Util\ResponseUtil;
use App\Entity\AuthType;
use App\Entity\AuthVerify;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Contracts\Translation\TranslatorInterface;

class AuthService
{
    private $entityManager;
    private $userPasswordHash;
    private $tokenStorage;
    private $session;
    private $lang;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $userPasswordHash,
        TokenStorageInterface $tokenStorage,
        private RequestStack $requestStack,
        TranslatorInterface $lang,
        private SettingService $setting,
        private SecurityService $security
    )
    {
        $this->entityManager = $entityManager;
        $this->userPasswordHash = $userPasswordHash;
        $this->tokenStorage = $tokenStorage;
        $this->lang = $lang;
        $this->setting = $setting;
        $this->security = $security;

        // We use request stack to get session because - because 
        // SessionInterface it cause issue when we access in controller
        $this->session = $requestStack->getSession();
    }

    /**
     * User Registration
     * 
     * This is used to register new customer
     * 
     * @param bool jsonResponse
     * @param string countrycode
     * @param string firstname
     * @param string middlename
     * @param string lastname
     * @param string email
     * @param string password
     */
    public function registerUser(
        bool $jsonResponse,
        string $countryCode,
        string $firtName,
        string $middleName,
        string $lastName,
        string $email,
        string $password
    )
    {
        try {
            // Validate Country
            $country = EntityUtil::findOneCountry($this->lang, $this->entityManager, $countryCode);

            // Exception
            if($country instanceof \Exception) throw new \Exception($country->getMessage());

            // Verify email
            $verifyEmail = EntityUtil::verifyUniqueUser($this->lang, $this->entityManager, $email);

            // Exception
            if($verifyEmail instanceof \Exception) throw new \Exception($verifyEmail->getMessage());
            
            // Verify password
            if($this->userPasswordVerify($password) instanceof \Exception) throw new \Exception($this->userPasswordVerify($password)->getMessage());
            
            
            // Prepaire User
            $user = new User();

            // Hash Password
            $passwordHash = $this->userPasswordHash->hashPassword($user, $password);

            // Prepaire Data
            $user->setFirstName($firtName);
            $user->setMiddleName($middleName);
            $user->setLastName($lastName);
            $user->setEmail($email);
            $user->setPassword($passwordHash);
            $user->setIsVerified(false);
            $user->setAuthFactorRegister(($this->setting->getValueByKey('auth_register') == 1) ? true : false);
            $user->setAuthFactorLogin(($this->setting->getValueByKey('auth_login') == 1) ? true : false);

            // Add User & Flush changes
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // Return User
            return ResponseUtil::response($jsonResponse, $user, 200, ['user' => $user->getFullName(), 'email' => $user->getEmail()], $this->lang->trans('auth.signup.success'));

        } catch (\Exception $th) {
            //throw $th;
            return ResponseUtil::response($jsonResponse, $th, 400, null, $th->getMessage());
        }
    }

    /**
     * Forget User Password
     * 
     * This is used to reset new password
     * none login user
     * 
     * @param bool jsonResponse
     * @param string email
     * @param string identifier
     * @param string token
     * @param string newPassword
     */
    public function forgetUserPassword(
        bool $jsonResponse,
        string $email,
        string $identifier,
        string $token,
        string $newPassword
    )
    {
        try {
            
            // Find User
            $user = EntityUtil::findOneUser($this->lang, $this->entityManager, $email);

            // Exception
            if($user instanceof \Exception) throw new \Exception($user->getMessage());
            
            // Verify Authentication
            $authentication = EntityUtil::findOneAuthVerify($this->lang, $this->entityManager, $user, $this->security->addUpdateUserDevice($user), $identifier, $token, true);

            // Exception
            if($authentication instanceof \Exception) throw new \Exception($authentication->getMessage());
            
            // Verify identifier
            if(!in_array($authentication->getAuthType()->getAuth()->getCode(), ['auth_reset_password'])) throw new \Exception($this->lang->trans('auth.not_valid'));
            
            // Hash Password
            $passwordHash = $this->userPasswordHash->hashPassword($user, $newPassword);

            // Update Data
            $user->setPassword($passwordHash);

            // Flush changes
            $this->entityManager->flush();

            // Remove & Flush Authentication
            $this->entityManager->remove($authentication);
            $this->entityManager->flush();

            // Return Response
            return ResponseUtil::response($jsonResponse, $user, 200, ['user' => $user->getFullName(), 'email' => $user->getEmail()], $this->lang->trans('auth.password_reset.success'));

        } catch (\Exception $th) {
            //throw $th;
            return ResponseUtil::response($jsonResponse, $th, 400, null, $th->getMessage());
        }
    }

    /**
     * Forget User Password
     * 
     * This is used to get all Auth
     * none login user
     * 
     * @param bool jsonResponse
     * @param string email
     */
    public function forgetUserPasswordFactor(
        bool $jsonResponse,
        string $email
    )
    {
        try {
            
            // Find User
            $user = EntityUtil::findOneUser($this->lang, $this->entityManager, $email);

            // Exception
            if($user instanceof \Exception) throw new \Exception($user->getMessage());
            
            // Get All Authentication
            $authentication = $this->factorAllTypeAuth($user, 'auth_reset_password');
            
            // Return Response
            return $authentication;

        } catch (\Exception $th) {
            //throw $th;
            return ResponseUtil::response($jsonResponse, $th, 400, null, $th->getMessage());
        }
    }

    /**
     * Forget User Password
     * 
     * This is used to submit new token
     * none login user
     * 
     * @param bool jsonResponse
     * @param string identifier
     * @param string email
     */
    public function forgetUserPasswordFactorSubmit(
        bool $jsonResponse,
        string $identifier,
        string $email
    )
    {
        try {
            
            // Find User
            $user = EntityUtil::findOneUser($this->lang, $this->entityManager, $email);

            // Exception
            if($user instanceof \Exception) throw new \Exception($user->getMessage());
            
            // Generate new Token
            $token = $this->factorAuthSubmit($jsonResponse, $user, 'auth_reset_password', $identifier);

            // Exception
            if($token instanceof \Exception) throw new \Exception($token->getMessage());

            // Return Response
            return $token;

        } catch (\Exception $th) {
            //throw $th;
            return ResponseUtil::response($jsonResponse, $th, 400, null, $th->getMessage());
        }
    }

    /**
     * Password Verify
     * 
     * Verify Password
     * 
     * @param string password
     */
    public function userPasswordVerify(
        string $password,
        int $length = 8
    )
    {
        try {
            
            // Hold Verify
            $verifyPassword = preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{'.$length.',}$/', $password);

            // Verify Password
            if(!$verifyPassword) throw new \Exception($this->lang->trans('auth.password_verify'));
            
            // Return Password
            return $password;

        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }
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

            // Return Response
            return ResponseUtil::response($jsonResponse, $user, 200, ['user' => $user->getFullName(), 'email' => $user->getEmail()], 'Authentication Successful');

        } catch (\Exception $th) {
            //throw $th;
            return ResponseUtil::response($jsonResponse, $th, 400, null, $th->getMessage());
        }
    }

    /**
     * 2-Factor Authentication
     * 
     * Get all available auth type
     * it uses for selection of auth in web, api
     * 
     * @param User user
     * @param string auth
     * 
     * @return array
     */
    public function factorAllTypeAuth(
        User $user,
        string $auth
    )
    {
        // Hold Auth Types
        $authTypes = ['2factor' => false, 'auth' => []];

        try {
            // Find Authentication
            $authentication = EntityUtil::findOneAuth($this->lang, $this->entityManager, $auth, true);

            // Exception
            if($authentication instanceof \Exception) throw new \Exception($authentication->getMessage());
            
            // Loop Authentication Type
            foreach ($authentication->getTypes() as $key => $type) {
                # Skip Inactive...
                if($type->getActive() == false) continue;

                // Find Primary Provider
                $provider = EntityUtil::findPrimaryAuthTypeProvider($this->lang, $this->entityManager, $type->getAuthWay()->getIdentifier());

                // Exception
                if($provider instanceof \Exception) continue;

                // Add Authentication
                $authTypes['auth'][] = [
                    'identifier' => $type->getIdentifier(),
                    'type' => $type->getAuthWay()->getVerifyType(),
                    'name' => $type->getAuthWay()->getName(),
                    'short_desc' => $type->getAuthWay()->getShortDesc(),
                    'long_desc' => $type->getAuthWay()->getLongDesc()
                ];
            }

            // Empty Auth Type
            if(empty($authTypes['auth'])) throw new \Exception($this->lang->trans('auth.not_active'));

            // Set Auth true
            $authTypes['2factor'] = true;

            // Return Response
            return ResponseUtil::response(true, $authentication, 200, $authTypes, $this->lang->trans('auth.required'));
            
        } catch (\Exception $th) {
            //throw $th;
            return ResponseUtil::response(true, $th, 400, $authTypes, $th->getMessage());
        }
    }

    /**
     * 2-Factor Authentication
     * 
     * Submit Auth Submission
     * 
     * @param bool jsonResponse
     * @param User user
     * @param string auth
     * @param string identifier
     * 
     */
    public function factorAuthSubmit(
        bool $jsonResponse,
        User $user,
        string $auth = null,
        string $identifier
    )
    {
        // Hold Response
        $response = ['2factor' => true];

        try {
            
            // Find Auth
            $authentication = EntityUtil::findOneAuthType($this->lang, $this->entityManager, $identifier, true);

            // Exception
            if($authentication instanceof \Exception) throw new \Exception($authentication->getMessage());

            // Verify auth identifier pass
            if($auth && !in_array($authentication->getAuth()->getCode(), [$auth])) throw new \Exception($this->lang->trans('auth.not_valid'));
            
            // Verify if Registration
            if($user->isAuthFactorRegister() != true && $authentication->getAuth()->getCode() == 'auth_register' || $user->isAuthFactorLogin() != true && $authentication->getAuth()->getCode() == 'auth_login') throw new \Exception("Sorry you can't use this service at the moment.");
            
            // Verify Type
            switch ($authentication->getAuthWay()->getVerifyType()) {
                case 'token':
                    $addUpdate = $this->factorAddUpdateAuthVerify($authentication, $user);
                    break;
                
                default:
                    throw new \Exception("Authentication not available, try again later");
                    break;
            }

            // Exception
            if($addUpdate instanceof \Exception) throw new \Exception($addUpdate->getMessage());
            
            // Send Notification

            // Return Response
            return ResponseUtil::response($jsonResponse, $addUpdate, 200, $response, $this->lang->trans('auth.confirm_token'));

        } catch (\Exception $th) {
            //throw $th;
            return ResponseUtil::response($jsonResponse, $th, 400, null, $th->getMessage());
        }
    }

    /**
     * 2-Factor Authentication
     * 
     * Verify Auth Submission
     * 
     * @param User user
     * @param string identifier
     * @param string token
     * 
     * @return array
     */
    public function factorAuthConfirmSubmit(
        User $user,
        string $identifier,
        string $token
    )
    {
        // Hold Response
        $response = ['2factor' => true];

        try {
            
            // Find Auth
            $auth = EntityUtil::findOneAuthVerify($this->lang, $this->entityManager, $user, $this->security->addUpdateUserDevice($user), $identifier, $token, true);

            // Exception
            if($auth instanceof \Exception) throw new \Exception($auth->getMessage());
            
            // Remove Auth Verify
            $this->entityManager->remove($auth);
            $this->entityManager->flush();

            // Update User
            if($auth->getAuthType()->getAuth()->getCode() == 'auth_register') {
                $user->setAuthFactorRegister(false);
                $this->entityManager->flush();
            }
            
            // Send Notification

            // Return Response
            return ResponseUtil::response(true, $user, 200, $response, $this->lang->trans('auth.success'));

        } catch (\Exception $th) {
            //throw $th;
            return ResponseUtil::response(true, $th, 400, null, $th->getMessage());
        }
    }

    /**
     * 2-Factor Authentication
     * 
     * Add & Update Verification
     * 
     * @param AuthType auth
     * @param User user
     * 
     * @return AuthVerify
     */
    public function factorAddUpdateAuthVerify(
        AuthType $authType,
        User $user,
    )
    {
        try {
            
            // Find Verify
            $verify = EntityUtil::findOneAuthVerify($this->lang, $this->entityManager, $user, $this->security->addUpdateUserDevice($user), $authType->getIdentifier(), null, true);

            // Verify Exist
            if(!$verify instanceof \Exception) {
                
                // Update Verify
                $verify->setActive(true);
                $verify->setToken(GenerateUtil::number(6));

                // Flush changes
                $this->entityManager->flush();

                // Return Verify
                return $verify;
            }

            // Prepaire Auth Verify
            $verify = new AuthVerify();

            // Prepaire Data
            $verify->setDate(new \DateTime());
            $verify->setAuthType($authType);
            $verify->setUser($user);
            $verify->setToken(GenerateUtil::number(6));
            $verify->setDevice($this->security->addUpdateUserDevice($user));
            $verify->setActive(true);

            // Add Data & Flush changes
            $this->entityManager->persist($verify);
            $this->entityManager->flush();

            // Return Auth Verify
            return $verify;

        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }
    }
}