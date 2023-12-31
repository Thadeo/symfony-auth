<?php
namespace App\Service;

use App\Entity\Settings;
use Doctrine\ORM\EntityManagerInterface;

class SettingService
{
    private $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Get all Value
     * 
     * @return array
     */
    public function getAllValue()
    {
        // Hold array
        $data = [];

        // Find Settings
        $settings = $this->entityManager->getRepository(Settings::class)->findAll();

        // Loop
        foreach ($settings as $setting) {

            // Format
            $data[$setting->getCode()] = $setting->getValue();
        }

        // Return array
        return $data;
    }

    /**
     * Get Value by Key
     * 
     * @param string key
     * @return ?string
     */
    public function getValueByKey(string $key)
    {
        // Find Setting
        $setting = $this->entityManager->getRepository(Settings::class)->findOneBy(['code' => $key]);

        // Verify Settings
        if ($setting) {

            // Return value
            return $setting->getValue();
        }

        // Return null
        return null;
    }
}