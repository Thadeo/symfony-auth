<?php
namespace App\Service;

use App\Component\Util\EntityUtil;
use App\Component\Util\FormatUtil;
use App\Component\Util\GenerateUtil;
use App\Component\Util\ResponseUtil;
use App\Entity\AuthType;
use App\Entity\AuthVerify;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Contracts\Translation\TranslatorInterface;

class AuthService
{
    private $entityManager;
    private $session;
    private $roles;

    public function __construct(
        EntityManagerInterface $entityManager,
        private TokenStorageInterface $tokenStorage,
        private RequestStack $requestStack,
        private TranslatorInterface $lang,
        private SettingService $setting,
        private SecurityService $security,
        private JWTTokenManagerInterface $jwtToken,
        RolesService $roles
    )
    {
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
        $this->lang = $lang;
        $this->setting = $setting;
        $this->security = $security;
        $this->jwtToken = $jwtToken;
        $this->roles = $roles;

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
     * @param string accounttype
     * @param string countrycode
     * @param string firstname
     * @param string middlename
     * @param string lastname
     * @param string birthdate
     * @param string gender
     * @param string email
     * @param string password
     */
    public function registerUser(
        bool $jsonResponse,
        string $accountType,
        string $countryCode,
        string $firtName,
        string $middleName,
        string $lastName,
        string $birthDate,
        string $gender,
        string $email,
        string $password
    )
    {
        try {
            
            // Validate Account Type
            $accountType = EntityUtil::findOneUserAccountType($this->lang, $this->entityManager, $accountType);

            // Exception
            if($accountType instanceof \Exception) throw new \Exception($accountType->getMessage());

            // Validate Country
            $country = EntityUtil::findOneCountry($this->lang, $this->entityManager, $countryCode);

            // Exception
            if($country instanceof \Exception) throw new \Exception($country->getMessage());

            // Verify email
            $verifyEmail = EntityUtil::verifyUniqueUser($this->lang, $this->entityManager, $email);

            // Exception
            if($verifyEmail instanceof \Exception) throw new \Exception($verifyEmail->getMessage());

            // Find gender
            $findGender = EntityUtil::findOneGenderType($this->lang, $this->entityManager, $gender);

            // Exception
            if($findGender instanceof \Exception) throw new \Exception($findGender->getMessage());
            
            // Verify password
            if($this->security->validateUserPassword($password) instanceof \Exception) throw new \Exception($this->security->validateUserPassword($password)->getMessage());
            
            
            // Prepaire User
            $user = new User();

            // Hash Password
            $passwordHash = $this->security->userPasswordHash($user, $password);

            // Exception
            if($passwordHash instanceof \Exception) throw new \Exception($passwordHash->getMessage());

            // Prepaire Data
            $user->setDate(new \DateTime());
            $user->setCountry($country);
            $user->setAccountType($accountType);
            $user->setFirstName($firtName);
            $user->setMiddleName($middleName);
            $user->setLastName($lastName);
            $user->setBirthDate(FormatUtil::dateToDateTime($birthDate));
            $user->setGender($findGender);
            $user->setEmail($email);
            $user->setPassword($passwordHash);
            $user->setIsVerified(false);
            $user->setAuthFactorRegister(($this->setting->getValueByKey('auth_register') == 1) ? true : false);
            $user->setAuthFactorLogin(($this->setting->getValueByKey('auth_login') == 1) ? true : false);
            $user->setMode('live');
            $user->setUpdatedDate(new \DateTime());

            // Add User & Flush changes
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // Add Permission
            $addRoles = $this->roles->addUpdateUserRole(false, $user);

            // Exception
            if($addRoles instanceof Exception) {
                // Remove User
                $this->entityManager->remove($user);
                $this->entityManager->flush();

                // Exception
                throw new \Exception($addRoles->getMessage());
            }

            // Add Activity
            $this->security->addUserActivity($user, 'auth_register', null, $user->getMode());

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
     * 
     * @param bool jsonResponse
     * @param User user
     * @param string identifier
     * @param string token
     * @param string newPassword
     */
    public function forgetUserPassword(
        bool $jsonResponse,
        User $user,
        string $identifier,
        string $token,
        string $newPassword
    )
    {
        try {
            
            // Verify Authentication
            $authentication = EntityUtil::findOneAuthVerify($this->lang, $this->entityManager, $user, $this->security->addUpdateUserDevice($user), $identifier, $token, true);

            // Exception
            if($authentication instanceof \Exception) throw new \Exception($authentication->getMessage());
            
            // Verify identifier
            if(!in_array($authentication->getAuthType()->getAuth()->getCode(), ['auth_reset_password'])) throw new \Exception($this->lang->trans('auth.not_valid'));
            
            // Hash Password
            $passwordHash = $this->security->userPasswordHash($user, $newPassword);

            // Exception
            if($passwordHash instanceof \Exception) throw new \Exception($passwordHash->getMessage());
            

            // Update Data
            $user->setPassword($passwordHash);

            // Flush changes
            $this->entityManager->flush();

            // Remove & Flush Authentication
            $this->entityManager->remove($authentication);
            $this->entityManager->flush();

            // Add Activity
            $this->security->addUserActivity($user, 'auth_reset_password', null, $user->getMode());

            // Remove Api User in session
            $this->session->remove('apiUser');

            // Return Response
            return ResponseUtil::response($jsonResponse, $user, 200, ['user' => $user->getFullName(), 'email' => $user->getEmail()], $this->lang->trans('auth.password_reset.success'));

        } catch (\Exception $th) {
            //throw $th;
            return ResponseUtil::response($jsonResponse, $th, 400, null, $th->getMessage());
        }
    }

    /**
     * User Login Factor
     * 
     * This is used to confirm factor
     * 
     * @param bool jsonResponse
     * @param User user
     * @param bool appAuth
     * @param string identifier
     * @param string token
     * @param string newPassword
     */
    public function userLoginFactorConfirm(
        bool $jsonResponse,
        User $user,
        bool $appAuth,
        string $identifier,
        string $token
    )
    {
        try {
            
            // Verify Authentication
            $authentication = EntityUtil::findOneAuthVerify($this->lang, $this->entityManager, $user, $this->security->addUpdateUserDevice($user), $identifier, $token, true);

            // Exception
            if($authentication instanceof \Exception) throw new \Exception($authentication->getMessage());
            
            // Verify identifier
            if(!in_array($authentication->getAuthType()->getAuth()->getCode(), ['auth_login'])) throw new \Exception($this->lang->trans('auth.not_valid'));
            
            // Remove & Flush Authentication
            $this->entityManager->remove($authentication);
            $this->entityManager->flush();

            // Add Activity
            $this->security->addUserActivity($user, 'auth_login', null, $user->getMode());

            // Update Session
            $this->security->updateSession($user);

            // Remove Api User in session
            $this->session->remove('apiUser');

            // App Authenticate
            if($appAuth) return $this->userAuthenticate(true, $user);

            // API Authenticate
            $token = $this->jwtToken->create($user);

            // Add Token Data
            $response['type'] = 'Bearer';
            $response['token'] = $token;

            // Return Response
            return ResponseUtil::response($jsonResponse, $user, 200, $response, $this->lang->trans('auth.success'));

        } catch (\Exception $th) {
            //throw $th;
            return ResponseUtil::response($jsonResponse, $th, 400, null, $th->getMessage());
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
            return ResponseUtil::response($jsonResponse, $user, 200, ['user' => $user->getFullName(), 'email' => $user->getEmail()], $this->lang->trans('auth.success'));

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

            // Add Activity
            $this->security->addUserActivity($user, $auth->getAuthType()->getAuth()->getCode(), null, $user->getMode());
            
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