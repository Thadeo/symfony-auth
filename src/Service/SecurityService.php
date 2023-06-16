<?php
namespace App\Service;

use App\Component\Util\EntityUtil;
use App\Component\Util\GenerateUtil;
use App\Entity\AuthType;
use App\Entity\AuthVerify;
use App\Entity\User;
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
}