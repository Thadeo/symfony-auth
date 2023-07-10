<?php
namespace App\Service;

use App\Component\Util\EntityUtil;
use App\Entity\User;
use App\Entity\UserActivity;
use App\Entity\UserActivityCategory;
use App\Entity\UserDevices;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityService
{
    private $entityManager;
    private $session;

    public function __construct(
        EntityManagerInterface $entityManager,
        private TokenStorageInterface $tokenStorage,
        private RequestStack $requestStack,
        private TranslatorInterface $lang,
        private SettingService $setting,
        private UserPasswordHasherInterface $userPasswordHash,
    )
    {
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
        $this->lang = $lang;
        $this->setting = $setting;
        $this->userPasswordHash = $userPasswordHash;

        // We use request stack to get session because - because 
        // SessionInterface it cause issue when we access in controller
        $this->session = $requestStack->getSession();
    }

    /**
     * User Device
     * 
     * Add & Update User Device
     * 
     * @param User user
     * 
     * @return UserDevices
     */
    public function addUpdateUserDevice(
        User $user,
    )
    {
        try {
            
            // Find Device
            $device = EntityUtil::findOneUserDevice($this->lang, $this->entityManager, $user, $_SERVER['HTTP_USER_AGENT'], true);

            // Device Exist
            if(!$device instanceof \Exception) return $device;

            // Find OS
            // Prepaire Device
            $device = new UserDevices();

            // Prepaire Data
            $device->setDate(new \DateTime());
            $device->setUser($user);
            //$device->setName();
            //$device->setOs();
            $device->setUserAgent($_SERVER['HTTP_USER_AGENT']);
            $device->setActive(true);
            $device->setUpdatedDate(new \DateTime());

            // Add Data & Flush changes
            $this->entityManager->persist($device);
            $this->entityManager->flush();

            // Find IP Address

            // Return Device
            return $device;

        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }
    }

    /**
     * Add User Activity
     * 
     * add activity records
     * 
     * @param User user
     * @param string action
     * @param string desc
     * @param string mode
     * 
     * @return UserActivity
     */
    public function addUserActivity(
        User $user,
        string $action,
        string $desc = null,
        string $mode
    )
    {
        try {
            
            // Find Category
            $category = $this->entityManager->getRepository(UserActivityCategory::class)->findOneBy(['code' => $action]);

            // Exception
            if(!$category) throw new \Exception("Action $action not exist");
            
            // Prepaire new Data
            $activity = new UserActivity;
            $activity->setDate(new \DateTime());
            $activity->setUser($user);
            $activity->setCategory($category);
            $activity->setDevice($this->addUpdateUserDevice($user));
            $activity->setLongDesc(($desc) ? $desc : $category->getLongDesc());
            $activity->setMode($mode);
            $activity->setUpdatedDate(new \DateTime());

            // Add Data
            $this->entityManager->persist($activity);

            // Flush changes
            $this->entityManager->flush();

            // Return true
            return $activity;
        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }
    }

    /**
     * Session
     * 
     * Update Current Session
     * 
     * @param User user
     * 
     * @return UserDevices
     */
    public function updateSession(
        User $user,
    )
    {
        try {
            
            // Find Session
            $session = EntityUtil::findOneSession($this->lang, $this->entityManager, $this->session->getId());

            // Session Exist
            if($session instanceof \Exception) throw new \Exception($this->lang->trans('security.auth.required'));

            // Update Session
            $session->setDate(new \DateTime());
            $session->setUser($user);
            $session->setDevice($this->addUpdateUserDevice($user));

            // Flush changes
            $this->entityManager->flush();

            // Return Session
            return $session;

        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }
    }

    /**
     * Validate User Password
     * 
     * Password Length
     * 
     * @param string password
     */
    public function validateUserPassword(
        string $password,
        int $length = 8
    )
    {
        try {
            
            // Hold Verify
            $verifyPassword = preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{'.$length.',}$/', $password);

            // Verify Password
            if(!$verifyPassword) throw new \Exception($this->lang->trans('security.password_validate.invalid'));
            
            // Return Password
            return $password;

        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }
    }

    /**
     * Verify User Password
     * 
     *Current Password
     * 
     * @param User user
     * @param string password
     */
    public function verifyUserPassword(
        User $user,
        string $password
    )
    {
        try {
            
            // Hold Verify
            $verifyPassword = $this->userPasswordHash->isPasswordValid($user, $password);

            // Verify Password
            if(!$verifyPassword) throw new \Exception($this->lang->trans('security.password_verify.invalid'));
            
            // Return User
            return $user;

        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }
    }

    /**
     * User Password Hash
     * 
     * Password Hash
     * 
     * @param User user
     * @param string password
     * @param int length
     */
    public function userPasswordHash(
        User $user,
        string $password,
        int $length = 8
    )
    {
        try {
            
            // Verify Password
            $verifyPassword = $this->validateUserPassword($password, $length);

            // Exception
            if($verifyPassword instanceof \Exception) throw new \Exception($verifyPassword->getMessage());
            
            // Hash Password
            $passwordHash = $this->userPasswordHash->hashPassword($user, $password);
            
            // Return Hash
            return $passwordHash;

        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }
    }

    /**
     * User Password Match
     * 
     * Password Match
     * 
     * @param User user
     * @param string password
     * @param string newPassword
     */
    public function userNewOldPasswordMatch(
        User $user,
        string $password,
        string $newPassword
    )
    {
        try {
            
            // Verify Password
            $verifyPassword = $this->verifyUserPassword($user, $password);

            // Exception
            if($verifyPassword instanceof \Exception) throw new \Exception($verifyPassword->getMessage());
            
            // Match Password
            if($password === $newPassword) throw new \Exception($this->lang->trans('security.password_match.invalid'));
            
            
            // Return user
            return $user;

        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }
    }
}