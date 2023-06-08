<?php
namespace App\Service;

use App\Component\Util\EntityUtil;
use App\Component\Util\ResponseUtil;
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

            // Add User & Flush changes
            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // Authentication
            if($this->setting->getValueByKey('auth_register') == 1) {
                // Authenticate
            }

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
            return ResponseUtil::response($jsonResponse, $th, 400, null, 'Authentication Failed '.$th->getMessage());
        }
    }

    /**
     * 2-Factor Authentication
     * 
     * @param User user
     * @param string auth
     */
    public function factorAuth(
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
                $provider = EntityUtil::findPrimaryAuthTypeProvider($this->lang, $this->entityManager, $auth);

                // Exception
                if($provider instanceof \Exception) throw new \Exception($provider->getMessage());

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
            if(empty($authTypes['auth'])) throw new \Exception("Authentication not active");

            // Set Auth true
            $authTypes['2factor'] = true;

            // Return Response
            return ResponseUtil::response(true, $authentication, 200, $authTypes, '2-factor authenticate required');
            
        } catch (\Exception $th) {
            //throw $th;
            return ResponseUtil::response(true, $th, 400, $authTypes, $th->getMessage());
        }
    }
}