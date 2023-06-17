<?php
namespace App\Service;

use App\Component\Util\EntityUtil;
use App\Component\Util\GenerateUtil;
use App\Entity\AuthType;
use App\Entity\AuthVerify;
use App\Entity\User;
use App\Entity\UserActivity;
use App\Entity\UserActivityCategory;
use App\Entity\UserDevices;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class SecurityService
{
    private $entityManager;
    private $tokenStorage;
    private $session;
    private $lang;

    public function __construct(
        EntityManagerInterface $entityManager,
        TokenStorageInterface $tokenStorage,
        private RequestStack $requestStack,
        TranslatorInterface $lang,
        private SettingService $setting
    )
    {
        $this->entityManager = $entityManager;
        $this->tokenStorage = $tokenStorage;
        $this->lang = $lang;
        $this->setting = $setting;

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
            if($session instanceof \Exception) throw new \Exception("Authentication Required");

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
}