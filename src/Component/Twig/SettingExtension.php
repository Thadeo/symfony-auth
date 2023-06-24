<?php
namespace App\Component\Twig;

use App\Service\RolesService;
use App\Service\SettingService;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class SettingExtension extends AbstractExtension implements GlobalsInterface
{
    private $tokenStorage;
    private $setting;
    private $roles;
    
    public function __construct(
        SettingService $setting,
        RolesService $roles,
        TokenStorageInterface $tokenStorage
    )
    {
        $this->setting = $setting;
        $this->roles = $roles;
        $this->tokenStorage = $tokenStorage;
    }

    public function getGlobals():array
    {
        $data =  [
            'user_role' => [],
            'user_permission' => [],
            'setting' => $this->setting->getAllValue()
        ];

        // Authentication Data only
        if(!empty($this->tokenStorage->getToken())) {
            
            // Query Permission
            $userRule =  $this->roles->userRole(true, $this->tokenStorage->getToken()->getUser())['data'];

            // Query Permission
            $userPermission =  $this->roles->userRolePermission(true, $this->tokenStorage->getToken()->getUser())['data'];
            
            // Verify if user ROLE not null
            if($userRule !== null) $data['user_role'] = $userRule;

            // Verify if user permission not null
            if($userPermission !== null) $data['user_permission'] = $userPermission;
        }

        return $data;
    }
}