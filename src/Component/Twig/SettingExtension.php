<?php
namespace App\Component\Twig;

use App\Service\SettingService;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class SettingExtension extends AbstractExtension implements GlobalsInterface
{
    private $setting;
    
    public function __construct(
        SettingService $setting
    )
    {
        $this->setting = $setting;
    }

    public function getGlobals():array
    {
        return [
            'setting' => $this->setting->getAllSettingsValue()
        ];
    }
}