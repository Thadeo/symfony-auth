<?php
namespace App\Component\Util;

use App\Entity\Auth;
use App\Entity\AuthType;
use App\Entity\AuthTypeProvider;
use App\Entity\AuthVerify;
use App\Entity\AuthWayProvider;
use App\Entity\Country;
use App\Entity\CountryState;
use App\Entity\Roles;
use App\Entity\Sessions;
use App\Entity\User;
use App\Entity\UserAccountType;
use App\Entity\UserAddress;
use App\Entity\UserDevices;
use App\Entity\UserGenderType;
use App\Entity\UserPhone;
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
     * @param string identifier
     * @param ?bool active
     * 
     * @return AuthType
     */
    public static function findOneAuthType(
        TranslatorInterface $lang,
        EntityManagerInterface $entityManager,
        string $identifier,
        bool $active = null
    )
    {
        try {
            // Find Auth Type
            $authType = $entityManager->getRepository(AuthType::class)->findOneType($identifier, $active);

            // Auth Type not exist
            if(!$authType) throw new \Exception("Auth identifier $identifier not exist");

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
     * @param string identifier
     * 
     * @return AuthTypeProvider
     */
    public static function findPrimaryAuthTypeProvider(
        TranslatorInterface $lang,
        EntityManagerInterface $entityManager,
        string $identifier
    )
    {
        try {
            // Find Auth Type
            $authType = $entityManager->getRepository(AuthWayProvider::class)->findOneProvider($identifier, true, true);

            // Auth Type not exist
            if(!$authType) throw new \Exception("Auth provider $identifier not exist");

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
     * @param User user
     * @param string identifier
     * @param string token
     * @param string device
     * @param bool active
     * 
     * @return AuthVerify
     */
    public static function findOneAuthVerify(
        TranslatorInterface $lang,
        EntityManagerInterface $entityManager,
        User $user,
        UserDevices $device = null,
        string $identifier,
        string $token = null,
        bool $active = null
    )
    {
        try {
            // Find Auth Verify
            $authVerify = $entityManager->getRepository(AuthVerify::class)->findOneVerify($user, $device, $identifier, $token, $active);

            // Auth Verify not exist
            if(!$authVerify) throw new \Exception(($token) ? "Token $token not valid" : "Auth identifier $identifier not exist");

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
     * @param TranslatorInterface lang
     * @param EntityManagerInterface entitymanager
     * @param string email
     * 
     * @return string
     */
    public static function verifyUniqueUser(
        TranslatorInterface $lang,
        EntityManagerInterface $entityManager,
        string $email
    )
    {
        try {

            // Find User
            $user = self::findOneUser($lang, $entityManager, $email);

            // User exist
            if(!$user instanceof \Exception) throw new \Exception("Email $email has been exist");

            // Return email
            return $email;

        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }
    }

    /**
     * Find One User
     * 
     * @param TranslatorInterface lang
     * @param EntityManagerInterface entitymanager
     * @param string email
     * 
     * @return User
     */
    public static function findOneUser(
        TranslatorInterface $lang,
        EntityManagerInterface $entityManager,
        string $email
    )
    {
        try {

            // Find User
            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

            // User exist
            if(!$user) throw new \Exception("Email $email not exist");

            // Return User
            return $user;

        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }
    }

    /**
     * Find One User Account Type
     * 
     * @param TranslatorInterface lang
     * @param EntityManagerInterface entitymanager
     * @param string code
     * @param bool isUserAccess
     * 
     * @return UserAccountType
     */
    public static function findOneUserAccountType(
        TranslatorInterface $lang,
        EntityManagerInterface $entityManager,
        string $code,
        bool $isUserAccess = true
    )
    {
         try {

            // Find Account Type
            $accountType = $entityManager->getRepository(UserAccountType::class)->findOneBy(['code' => $code, 'isUserAccess' => $isUserAccess]);

            // Account Type not exist
            if(!$accountType) throw new \Exception("Account Type $code not exist");

            // Return Account Type
            return $accountType;

        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }
    }

    /**
     * Find All User Account Type
     * 
     * @param TranslatorInterface lang
     * @param EntityManagerInterface entitymanager
     * 
     * @return UserAccountType
     */
    public static function findAllUserAccountType(
        TranslatorInterface $lang,
        EntityManagerInterface $entityManager,
        bool $isUserAccess = true
    )
    {
         try {

            // Find Account Type
            $accountType = $entityManager->getRepository(UserAccountType::class)->findBy(['isUserAccess' => $isUserAccess]);

            // Account Type not exist
            if(!$accountType) throw new \Exception("Account type not available");

            // Return Account Type
            return $accountType;

        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }
    }

    /**
     * Find One Country
     * 
     * @param TranslatorInterface lang
     * @param EntityManagerInterface entitymanager
     * @param string code
     * 
     * @return Country
     */
    public static function findOneCountry(
        TranslatorInterface $lang,
        EntityManagerInterface $entityManager,
        string $code = null
    )
    {
         try {

            // Find Country
            $country = $entityManager->getRepository(Country::class)->findOneBy(['code' => $code, 'active' => true]);

            // Country not exist
            if(!$country) throw new \Exception("Country $code not exist");

            // Return Country
            return $country;

        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }
    }

     /**
     * Find All Country
     * 
     * @param TranslatorInterface lang
     * @param EntityManagerInterface entitymanager
     * @param string country
     * @param int page
     * @param int perPage
     * @param string orderBy
     * @param string orderColumn
     * 
     * @return array
     */
    public static function findAllCountry(
        TranslatorInterface $lang,
        EntityManagerInterface $entityManager,
        string $country = null,
        int $page = 1,
        int $perPage = 10,
        string $orderBy = null,
        string $orderColumn = null
    )
    {
         try {

            // Find Country
            $country = $entityManager->getRepository(Country::class)->findAllCountry($country, $page, $perPage, $orderBy, $orderColumn);

            // Country not exist
            if(empty($country['data'])) throw new \Exception("Country not found");

            // Return Country
            return $country;

        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }
    }

    /**
     * Find One Country State
     * 
     * @param TranslatorInterface lang
     * @param EntityManagerInterface entitymanager
     * @param Country country
     * @param string state
     * 
     * @return CountryState
     */
    public static function findOneCountryState(
        TranslatorInterface $lang,
        EntityManagerInterface $entityManager,
        string $country,
        string $state
    )
    {
         try {

            // Find Country State
            $countryState = $entityManager->getRepository(CountryState::class)->findOneState($country, $state);

            // Country State not exist
            if(!$countryState) throw new \Exception("State $state not found");

            // Return Country State
            return $countryState;

        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }
    }

    /**
     * Find All Country State
     * 
     * @param TranslatorInterface lang
     * @param EntityManagerInterface entitymanager
     * @param string country
     * @param string state
     * @param int page
     * @param int perPage
     * @param string orderBy
     * @param string orderColumn
     * 
     * @return array
     */
    public static function findAllCountryState(
        TranslatorInterface $lang,
        EntityManagerInterface $entityManager,
        string $country,
        string $state = null,
        int $page = 1,
        int $perPage = 10,
        string $orderBy = null,
        string $orderColumn = null
    )
    {
         try {

            // Find State
            $states = $entityManager->getRepository(CountryState::class)->findAllState($country, $state, $page, $perPage, $orderBy, $orderColumn);

            // Country not exist
            if(empty($states['data'])) throw new \Exception("States not found");

            // Return State
            return $states;

        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }
    }

    /**
     * Find All Gender Type
     * 
     * @param TranslatorInterface lang
     * @param EntityManagerInterface entitymanager
     * 
     * @return UserGenderType
     */
    public static function findAllGenderType(
        TranslatorInterface $lang,
        EntityManagerInterface $entityManager
    )
    {
        try {

            // Find Gender
            $findGender = $entityManager->getRepository(UserGenderType::class)->findBy(['active' => true]);

            // Gender not exist
            if(!$findGender) throw new \Exception("Gender not exist");

            // Return Gender
            return $findGender;

        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }
    }

    /**
     * Find One Gender Type
     * 
     * @param TranslatorInterface lang
     * @param EntityManagerInterface entitymanager
     * @param string gender
     * 
     * @return UserGenderType
     */
    public static function findOneGenderType(
        TranslatorInterface $lang,
        EntityManagerInterface $entityManager,
        string $gender
    )
    {
        try {

            // Find Gender
            $findGender = $entityManager->getRepository(UserGenderType::class)->findOneBy(['code' => $gender]);

            // Gender not exist
            if(!$findGender) throw new \Exception("Gender $gender not exist");

            // Return Gender
            return $findGender;

        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }
    }

    /**
     * Find One User Device
     * 
     * @param TranslatorInterface lang
     * @param EntityManagerInterface entitymanager
     * @param User user
     * @param string device
     * @param bool active
     * 
     * @return UserDevices
     */
    public static function findOneUserDevice(
        TranslatorInterface $lang,
        EntityManagerInterface $entityManager,
        User $user,
        string $device = null,
        bool $active = null
    )
    {
        try {
            // Find Device
            $device = $entityManager->getRepository(UserDevices::class)->findOneDevice($user, $device, $active);

            // Auth Verify not exist
            if(!$device) throw new \Exception("Device not exist");

            // Verify if is active
            if($active && !$device->isActive()) throw new \Exception("Device not active");

            // Return Device
            return $device;

        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }
    }

    /**
     * Find One Session
     * 
     * @param TranslatorInterface lang
     * @param EntityManagerInterface entitymanager
     * @param string sessionid
     * 
     * @return Sessions
     */
    public static function findOneSession(
        TranslatorInterface $lang,
        EntityManagerInterface $entityManager,
        string $sessionid
    )
    {
        try {

            // Find Session
            $session = $entityManager->getRepository(Sessions::class)->findOneBy(['ids' => $sessionid]);

            // Session exist
            if(!$session) throw new \Exception("Session not exist");

            // Return User
            return $session;

        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }
    }

    /**
     * Find One Role By User Account Type
     * 
     * @param TranslatorInterface lang
     * @param EntityManagerInterface entitymanager
     * @param UserAccountType account
     * 
     * @return Roles
     */
    public static function findOneRoleByUserAccountType(
        TranslatorInterface $lang,
        EntityManagerInterface $entityManager,
        UserAccountType $account
    )
    {
         try {

            // Find Role
            $role = $entityManager->getRepository(Roles::class)->findOneBy(['accountType' => $account]);

            // Role Type not exist
            if(!$role) throw new \Exception("Role for account type ".$account->getCode()." not exist");

            // Return Role
            return $role;

        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }
    }

    /**
     * Find Primary Phone
     * 
     * @param TranslatorInterface lang
     * @param EntityManagerInterface entitymanager
     * @param User user
     * 
     * @return UserPhone
     */
    public static function findPrimaryPhone(
        TranslatorInterface $lang,
        EntityManagerInterface $entityManager,
        User $user
    )
    {
        try {

            // Find Phone
            $phone = $entityManager->getRepository(UserPhone::class)->findOneBy(['user' => $user, 'isPrimary' => true]);

            // Phone not exist
            if(!$phone) throw new \Exception("Primary Phone not exist");

            // Return Phone
            return $phone;

        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }
    }

    /**
     * Find One Phone
     * 
     * @param TranslatorInterface lang
     * @param EntityManagerInterface entitymanager
     * @param User user
     * @param string identifier
     * 
     * @return UserPhone
     */
    public static function findOnePhone(
        TranslatorInterface $lang,
        EntityManagerInterface $entityManager,
        User $user,
        string $identifier
    )
    {
        try {

            // Find Phone
            $phone = $entityManager->getRepository(UserPhone::class)->findOnePhone($user, $identifier);

            // Phone not exist
            if(!$phone) throw new \Exception("Phone not exist");

            // Return Phone
            return $phone;

        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }
    }

    /**
     * Find All Phone
     * 
     * @param TranslatorInterface lang
     * @param EntityManagerInterface entitymanager
     * @param string search
     * @param string country
     * @param bool isPrimary
     * 
     */
    public static function findAllPhone(
        TranslatorInterface $lang,
        EntityManagerInterface $entityManager,
        User $user,
        string $search = null,
        string $country = null,
        bool $isPrimary = null
    )
    {
        try {

            // Find Phone
            $phone = $entityManager->getRepository(UserPhone::class)->findAllPhone($user, $search, $country, $isPrimary);

            // Phone not exist
            if(!$phone) throw new \Exception("Phones not found");

            // Return Phone
            return $phone;

        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }
    }

    /**
     * Find Primary Address
     * 
     * @param TranslatorInterface lang
     * @param EntityManagerInterface entitymanager
     * @param User user
     * 
     * @return UserAddress
     */
    public static function findPrimaryAddress(
        TranslatorInterface $lang,
        EntityManagerInterface $entityManager,
        User $user
    )
    {
        try {

            // Find Address
            $address = $entityManager->getRepository(UserAddress::class)->findOneBy(['user' => $user, 'isPrimary' => true]);

            // Address not exist
            if(!$address) throw new \Exception("Primary address not exist");

            // Return Address
            return $address;

        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }
    }

    /**
     * Find One Address By Postal Code
     * 
     * @param TranslatorInterface lang
     * @param EntityManagerInterface entitymanager
     * @param User user,
     * @param string postalCode
     * 
     * @return UserAddress
     */
    public static function findOneAddressByPostalCode(
        TranslatorInterface $lang,
        EntityManagerInterface $entityManager,
        User $user,
        string $postalCode
    )
    {
        try {

            // Find Address
            $address = $entityManager->getRepository(UserAddress::class)->findOneBy(['user' => $user, 'postalCode' => $postalCode]);

            // Address not exist
            if(!$address) throw new \Exception("Address not exist");

            // Return Address
            return $address;

        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }
    }

    /**
     * Find One Address
     * 
     * @param TranslatorInterface lang
     * @param EntityManagerInterface entitymanager
     * @param User user,
     * @param string identifier
     * @param bool isActive
     * 
     * @return UserAddress
     */
    public static function findOneAddress(
        TranslatorInterface $lang,
        EntityManagerInterface $entityManager,
        User $user,
        string $identifier,
        bool $isActive = null
    )
    {
        try {

            // Find Address
            $address = $entityManager->getRepository(UserAddress::class)->findOneAddress($user, $identifier, $isActive);

            // Address not exist
            if(!$address) throw new \Exception("Address not exist");

            // Return Address
            return $address;

        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }
    }

    /**
     * Find All Address
     * 
     * @param TranslatorInterface lang
     * @param EntityManagerInterface entitymanager
     * @param string search
     * @param string country
     * @param string state
     * @param bool isPrimary
     * 
     */
    public static function findAllAddress(
        TranslatorInterface $lang,
        EntityManagerInterface $entityManager,
        User $user,
        string $search = null,
        string $country = null,
        string $state = null,
        bool $isPrimary = null
    )
    {
        try {

            // Find Address
            $address = $entityManager->getRepository(UserAddress::class)->findAllAddress($user, $search, $country, $state, $isPrimary);

            // Address not exist
            if(!$address) throw new \Exception("Address not found");

            // Return Address
            return $address;

        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }
    }
}