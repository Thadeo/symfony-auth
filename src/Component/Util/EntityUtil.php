<?php
namespace App\Component\Util;

use App\Entity\Auth;
use App\Entity\AuthType;
use App\Entity\AuthTypeProvider;
use App\Entity\AuthVerify;
use App\Entity\Country;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Entity Util
 * 
 * Basic Entity Util
 */
class EntityUtil
{
    /**
     * Find One Authentication
     * 
     * @param TranslatorInterface lang
     * @param EntityManagerInterface entitymanager
     * @param string auth
     * @param ?bool active
     * 
     * @return Auth
     */
    public static function findOneAuth(
        TranslatorInterface $lang,
        EntityManagerInterface $entityManager,
        string $auth,
        bool $active = null
    )
    {
        try {
            // Find Authentication
            $authentication = $entityManager->getRepository(Auth::class)->findOneBy(['code' => $auth]);

            // Authentication not exist
            if(!$authentication) throw new \Exception("Auth $auth not exist");

            // Verify if is active
            if($active && !$authentication->getActive()) throw new \Exception("Authentication not available");

            // Return Authentication
            return $authentication;

        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }
    }

    /**
     * Find One Authentication Type
     * 
     * @param TranslatorInterface lang
     * @param EntityManagerInterface entitymanager
     * @param string auth
     * @param ?bool active
     * 
     * @return AuthType
     */
    public static function findOneAuthType(
        TranslatorInterface $lang,
        EntityManagerInterface $entityManager,
        string $auth,
        bool $active = null
    )
    {
        try {
            // Find Auth Type
            $authType = $entityManager->getRepository(AuthType::class)->findOneBy(['code' => $auth]);

            // Auth Type not exist
            if(!$authType) throw new \Exception("Auth type $auth not exist");

            // Verify if is active
            if($active && !$authType->getActive()) throw new \Exception("Authentication type not active");

            // Return Auth Type
            return $authType;

        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }
    }

    /**
     * Find All Authentication Type Provider
     * 
     * @param TranslatorInterface lang
     * @param EntityManagerInterface entitymanager
     * @param string auth
     * @param ?bool active
     * 
     * @return AuthTypeProvider
     */
    public static function findAllAuthTypeProvider(
        TranslatorInterface $lang,
        EntityManagerInterface $entityManager,
        string $auth,
        bool $active = null
    )
    {
        try {
            // Find Auth Type
            $authType = $entityManager->getRepository(AuthTypeProvider::class)->findAllProvider($auth, $active);

            // Auth Type not exist
            if(!$authType) throw new \Exception("Auth type $auth not exist");

            // Return Auth Type
            return $authType;

        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }
    }

    /**
     * Find One Authentication Type Provider
     * 
     * @param TranslatorInterface lang
     * @param EntityManagerInterface entitymanager
     * @param string auth
     * @param ?bool active
     * 
     * @return AuthTypeProvider
     */
    public static function findOneAuthTypeProvider(
        TranslatorInterface $lang,
        EntityManagerInterface $entityManager,
        string $auth,
        bool $active = null
    )
    {
        try {
            // Find Auth Type
            $authType = $entityManager->getRepository(AuthTypeProvider::class)->findOneBy(['code' => $auth]);

            // Auth Type not exist
            if(!$authType) throw new \Exception("Auth type provider $auth not exist");

            // Verify if is active
            if($active && !$authType->getActive()) throw new \Exception("Authentication not active");

            // Return Auth Type
            return $authType;

        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }
    }

    /**
     * Find Primary Authentication Type Provider
     * 
     * @param TranslatorInterface lang
     * @param EntityManagerInterface entitymanager
     * @param string auth
     * 
     * @return AuthTypeProvider
     */
    public static function findPrimaryAuthTypeProvider(
        TranslatorInterface $lang,
        EntityManagerInterface $entityManager,
        string $auth
    )
    {
        try {
            // Find Auth Type
            $authType = $entityManager->getRepository(AuthTypeProvider::class)->findOneProvider($auth, true, true);

            // Auth Type not exist
            if(!$authType) throw new \Exception("Auth type provider $auth not exist");

            // Return Auth Type
            return $authType;

        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }
    }

    /**
     * Find One Authentication Verify
     * 
     * @param TranslatorInterface lang
     * @param EntityManagerInterface entitymanager
     * @param string auth
     * @param ?string token
     * @param ?string device
     * @param ?bool active
     * 
     * @return AuthVerify
     */
    public static function findOneAuthVerify(
        TranslatorInterface $lang,
        EntityManagerInterface $entityManager,
        string $auth,
        string $token = null,
        string $device = null,
        bool $active = null
    )
    {
        try {
            // Find Auth Verify
            $authVerify = $entityManager->getRepository(AuthVerify::class)->findOneVerify($auth, $token, $device, $active);

            // Auth Verify not exist
            if(!$authVerify) throw new \Exception(($token) ? "Token $token not valid" : "Auth $auth not exist");

            // Verify if is active
            if($active && !$authVerify->getActive()) throw new \Exception("You have already verify");

            // Return Auth Verify
            return $authVerify;

        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }
    }

    /**
     * Verify Unique User
     * 
     * @param EntityManagerInterface entitymanager
     * @param string email
     */
    public static function verifyUniqueUser(
        TranslatorInterface $lang,
        EntityManagerInterface $entityManager,
        string $email
    )
    {
        try {

            // Find User
            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

            // User exist
            if($user) throw new \Exception("Email $email has been exist");

            // Return User
            return $user;

        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }
    }

    /**
     * Find One Country
     * 
     * @param EntityManagerInterface entitymanager
     * @param array code
     */
    public static function findOneCountry(
        TranslatorInterface $lang,
        EntityManagerInterface $entityManager,
        string $code = null
    )
    {
         try {

            // Find Country
            $country = $entityManager->getRepository(Country::class)->findOneBy(['code' => $code]);

            // Country not exist
            if(!$country) throw new \Exception("Country $code not exist");

            // Return Country
            return $country;

        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }
    }
}