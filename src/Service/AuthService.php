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
        private SettingService $setting
    )
    {
        $this->entityManager = $entityManager;
        $this->userPasswordHash = $userPasswordHash;
        $this->tokenStorage = $tokenStorage;
        $this->lang = $lang;
        $this->setting = $setting;

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

            // Return User
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
                $provider = EntityUtil::findPrimaryAuthTypeProvider($this->lang, $this->entityManager, $type->getCode());

                // Exception
                if($provider instanceof \Exception) continue;

                // Add Authentication
                $authTypes['auth'][] = [
                    'verify_type' => $type->getVerifyType(),
                    'type' => $type->getCode(),
                    'name' => $type->getName(),
                    'short_desc' => $type->getShortDesc(),
                    'long_desc' => $type->getLongDesc()
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
     * @param User user
     * @param string authType
     * 
     * @return array
     */
    public function factorAuthSubmit(
        User $user,
        string $authType
    )
    {
        // Hold Response
        $response = ['2factor' => true];

        try {
            
            // Find Auth
            $auth = EntityUtil::findOneAuthType($this->lang, $this->entityManager, $authType, true);

            // Exception
            if($auth instanceof \Exception) throw new \Exception($auth->getMessage());
            
            // Verify Type
            switch ($auth->getVerifyType()) {
                case 'token':
                    $addUpdate = $this->factorAddUpdateAuthVerify($auth, $user);
                    break;
                
                default:
                    throw new \Exception("Authentication not available, try again later");
                    break;
            }

            // Exception
            if($addUpdate instanceof \Exception) throw new \Exception($addUpdate->getMessage());
            
            // Send Notification

            // Return Response
            return ResponseUtil::response(true, $addUpdate, 200, $response, $this->lang->trans('auth.confirm_token'));

        } catch (\Exception $th) {
            //throw $th;
            return ResponseUtil::response(true, $th, 400, null, $th->getMessage());
        }
    }

    /**
     * 2-Factor Authentication
     * 
     * Verify Auth Submission
     * 
     * @param User user
     * @param string authType
     * @param string token
     * 
     * @return array
     */
    public function factorAuthConfirmSubmit(
        User $user,
        string $authType,
        string $token
    )
    {
        // Hold Response
        $response = ['2factor' => true];

        try {
            
            // Find Auth
            $auth = EntityUtil::findOneAuthVerify($this->lang, $this->entityManager, $authType, $token, $_SERVER['HTTP_USER_AGENT'], true);

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
            $verify = EntityUtil::findOneAuthVerify($this->lang, $this->entityManager, $authType->getCode(), null, $_SERVER['HTTP_USER_AGENT'], true);

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
            $verify->setDevice($_SERVER['HTTP_USER_AGENT']);
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