<?php
namespace App\Service;

use App\Component\Util\EntityUtil;
use App\Component\Util\ResponseUtil;
use App\Entity\Roles;
use App\Entity\RolesPermission;
use App\Entity\User;
use App\Entity\UserCustomRoles;
use App\Entity\UserRoles;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RolesService
{
    private $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        private TranslatorInterface $lang
    )
    {
        $this->entityManager = $entityManager;
        $this->lang = $lang;
    }

    /**
     * User Role
     * 
     * Roles
     * 
     * @param bool jsonResponse
     * @param User user
     */
    public function userRole(
        bool $jsonResponse,
        User $user
    )
    {
        try {

            // Check if rule available
            if($user->getRole() == null) throw new \Exception($this->lang->trans('role.not_found'));
            
            // Get Role
            $role = ($user->getRole()->getCustom()) ? $user->getRole()->getCustom() : $user->getRole()->getRole();
            
            // Return Response
            return ResponseUtil::response($jsonResponse, $role, 200, self::formatResponseRole($role), $this->lang->trans('role.action.success'));

        } catch (\Exception $th) {
            //throw $th;
            return ResponseUtil::response($jsonResponse, $th, 400, [], $th->getMessage());
        }
    }

    /**
     * Format Response Role
     * 
     * Response Role
     * 
     * @param mixed Roles or UserCustomRoles
     */
    private static function formatResponseRole(
        $role
    )
    {
        // Role
        $data = [
            'name' => $role->getName(),
            'code' => $role->getCode(),
            'desc' => $role->getLongDesc()
        ];

        // Return data
        return $data;
    }

    /**
     * User Roles Permission
     * 
     * Permission
     * 
     * @param bool jsonResponse
     * @param User user
     */
    public function userRolePermission(
        bool $jsonResponse,
        User $user
    )
    {
        try {

            // Check if rule available
            if($user->getRole() == null) throw new \Exception($this->lang->trans('role.not_found'));

            // Get Roles Permissions
            $permissions = ($user->getRole()->getCustom()) ? $this->formatCustomUserRolePermission($jsonResponse, $user->getRole()->getCustom()) : $this->formatDefaultUserRolePermission($jsonResponse, $user->getRole()->getRole());

            // Permission not exist
            if($permissions instanceof \Exception) throw new \Exception($permissions->getMessage());

            // Return Response
            return $permissions;

        } catch (\Exception $th) {
            //throw $th;
            return ResponseUtil::response($jsonResponse, $th, 400, [], $th->getMessage());
        }
    }

    /**
     * Format User Roles Permission
     * 
     * Default Permission
     * 
     * @param bool jsonResponse
     * @param Roles permission
     */
    private function formatDefaultUserRolePermission(
        bool $jsonResponse,
        Roles $roles
    )
    {
        try {

            // Permission not exist
            if($roles->getRolesPermissions() == null) throw new \Exception($this->lang->trans('role.permission.not_found'));
            
            // Get Permission
            $permissions = $roles->getRolesPermissions();

            // Hold Data
            $data = [];

            // Json Response data
            if($jsonResponse) {
                // Loop Permissions
                foreach ($permissions as $key => $permission) {
                    # code...
                    $data[] =  self::formatResponseRolePermission($permission);
                }
            }

            // Return User
            return ResponseUtil::response($jsonResponse, $permissions, 200, $data, $this->lang->trans('role.action.success'));

        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }
    }

    /**
     * Format User Roles Permission
     * 
     * Custom Permission
     * 
     * @param bool jsonResponse
     * @param UserCustomRoles permission
     */
    private function formatCustomUserRolePermission(
        bool $jsonResponse,
        UserCustomRoles $roles
    )
    {
        try {

            // Permission not exist
            if($roles->getUserCustomRolesPermissions() == null) throw new \Exception($this->lang->trans('role.permission.not_found'));
            
            // Get Permission
            $permissions = $roles->getUserCustomRolesPermissions();

            // Hold Data
            $data = [];

            // Json Response data
            if($jsonResponse) {
                // Loop Permissions
                foreach ($permissions as $key => $permission) {
                    # code...
                    $data[] = self::formatResponseRolePermission($permission->getPermission());
                }
            }

            // Return User
            return ResponseUtil::response($jsonResponse, $permissions, 200, $data, $this->lang->trans('role.action.success'));

        } catch (\Exception $th) {
            //throw $th;
            return $th;
        }
    }

    /**
     * Format Response Roles Permission
     * 
     * Response Permission
     * 
     * @param RolesPermission permission
     */
    private static function formatResponseRolePermission(
        RolesPermission $permission
    )
    {
        // Permission
        $data = [
            'role' => $permission->getRole()->getName(),
            'name' => $permission->getName(),
            'code' => $permission->getCode(),
            'desc' => $permission->getLongDesc()
        ];

        // Return data
        return $data;
    }

    /**
     * User Role
     * 
     * add/update role for user
     * 
     * @param bool jsonResponse
     * @param User user
     * @param UserCustomRoles customRole
     */
    public function addUpdateUserRole(
        bool $jsonResponse,
        User $user,
        UserCustomRoles $customRole = null
    ) {
        try {

            // Find Role
            $findRole = EntityUtil::findOneRoleByUserAccountType($this->lang, $this->entityManager, $user->getAccountType());

            // Exception
            if($findRole instanceof \Exception) throw new \Exception($findRole->getMessage());

            // Get Roles
            $roles = ($customRole) ? $customRole : $findRole;

            // Update role if already added
            if($user->getRole()) {

                // Add Role if custom role empty
                if($customRole == null) $user->getRole()->setRole($findRole);
                
                // Add Custom Role if available
                if($customRole) $user->getRole()->setCustom($customRole);
               
                // Update Date
                $user->getRole()->setUpdatedDate(new \DateTime());

                // Flush Changes
                $this->entityManager->flush();

                // Return Response
                return ResponseUtil::response($jsonResponse, $roles, 200, self::formatResponseRole($roles), $this->lang->trans('role.action.success'));
            }
            
            // Prepaire & add new Role
            $role = new UserRoles();
            $role->setDate(new \DateTime());
            $role->setUser($user);
            
            // Add Role if custom role empty
            if($customRole == null) $role->setRole($findRole);
            
            // Add Custom Role if available
            if($customRole) $role->setCustom($customRole);
            $role->setUpdatedDate(new \DateTime());

            // Add Data & Flush Changes
            $this->entityManager->persist($role);
            $this->entityManager->flush();

            // Return Response
            return ResponseUtil::response($jsonResponse, $roles, 200, self::formatResponseRole($roles), $this->lang->trans('role.action.success'));

        } catch (\Exception $th) {
            //throw $th;
            return ResponseUtil::response($jsonResponse, $th, 400, null, $th->getMessage());
        }
    }
}