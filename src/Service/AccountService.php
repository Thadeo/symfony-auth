<?php
namespace App\Service;

use App\Component\Util\EntityUtil;
use App\Component\Util\FormatUtil;
use App\Component\Util\ResponseUtil;
use App\Entity\User;
use App\Entity\UserPhone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class AccountService
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
     * Account Type
     * 
     * get All Account Type
     * 
     * @param bool jsonResponse
     */
    public function allAccountType(
        bool $jsonResponse
    )
    {
        try {

            // Find Account Type
            $accountType = EntityUtil::findAllUserAccountType($this->lang, $this->entityManager);

            // Exception
            if($accountType instanceof \Exception) throw new \Exception($accountType->getMessage());
            
            // Hold data
            $data = [];

            // Loop Account Type
            foreach ($accountType as $key => $account) {
                # code...
                $data[] = [
                    'name' => $account->getName(),
                    'code' => $account->getCode()
                ];
            }
            
            // Return Response
            return ResponseUtil::response($jsonResponse, $accountType, 200, $data, $this->lang->trans('role.action.success'));

        } catch (\Exception $th) {
            //throw $th;
            return ResponseUtil::response($jsonResponse, $th, 400, [], $th->getMessage());
        }
    }

    /**
     * User Phone
     * 
     * Add Phone
     * 
     * @param bool jsonResponse
     * @param User user
     * @param string country
     * @param string phoneNumber
     */
    public function addPhone(
        bool $jsonResponse,
        User $user,
        string $country,
        string $phoneNumber
    )
    {
        try {

            // Find Country
            $country = EntityUtil::findOneCountry($this->lang, $this->entityManager, $country);

            // Exception
            if($country instanceof \Exception) throw new \Exception($country->getMessage());

            // Format phone number
            $phoneNumber = FormatUtil::phoneNumber($country->getDialCode(), $phoneNumber);

            // Find phone if exist
            $findPhone = EntityUtil::findOnePhone($this->lang, $this->entityManager, $user, $phoneNumber);

            // Phone Exist
            if(!$findPhone instanceof \Exception) throw new \Exception($this->lang->trans('account.phone.exist'));
            

            // Find Primary
            $findPrimary = EntityUtil::findPrimaryPhone($this->lang, $this->entityManager, $user);

            // Is Primary
            $isPrimary = ($findPrimary instanceof \Exception) ? true : false;

            // Prepaired DB
            $phone = new UserPhone();
            $phone->setUser($user);
            $phone->setDate(new \DateTime());
            $phone->setCountry($country);
            $phone->setPhone($phoneNumber);
            $phone->setIsPrimary($isPrimary);
            $phone->setActive(true);
            $phone->setUpdatedDate(new \DateTime());

            // Add Date & Flush Changes
            $this->entityManager->persist($phone);
            $this->entityManager->flush();
            
            // Return Response
            return ResponseUtil::response($jsonResponse, $phone, 200, self::formatPhoneDetails($phone), $this->lang->trans('account.action.success'));

        } catch (\Exception $th) {
            //throw $th;
            return ResponseUtil::response($jsonResponse, $th, 400, [], $th->getMessage());
        }
    }

    /**
     * User Phone
     * 
     * Update Phone
     * 
     * @param bool jsonResponse
     * @param User user
     * @param string country
     * @param string oldPhoneNumber
     * @param string phoneNumber
     */
    public function updatePhone(
        bool $jsonResponse,
        User $user,
        string $country,
        string $oldPhoneNumber,
        string $phoneNumber
    )
    {
        try {

            // Find Country
            $country = EntityUtil::findOneCountry($this->lang, $this->entityManager, $country);

            // Exception
            if($country instanceof \Exception) throw new \Exception($country->getMessage());

            // Format phone number
            $phoneNumber = FormatUtil::phoneNumber($country->getDialCode(), $phoneNumber);

            // Find old phone
            $phone = EntityUtil::findOnePhone($this->lang, $this->entityManager, $user, $oldPhoneNumber);

            // Phone not Exist
            if($phone instanceof \Exception) throw new \Exception($phone->getMessage());

            // Find new phone if exist
            $newPhone = EntityUtil::findOnePhone($this->lang, $this->entityManager, $user, $phoneNumber);

            // New Phone Exist
            if(!$newPhone instanceof \Exception) throw new \Exception($this->lang->trans('account.phone.exist'));
            
            // Update new phone
            $phone->setCountry($country);
            $phone->setPhone($phoneNumber);
            $phone->setUpdatedDate(new \DateTime());

            // Flush Changes
            $this->entityManager->flush();

            // Return Response
            return ResponseUtil::response($jsonResponse, $phone, 200, self::formatPhoneDetails($phone), $this->lang->trans('account.action.success'));

        } catch (\Exception $th) {
            //throw $th;
            return ResponseUtil::response($jsonResponse, $th, 400, [], $th->getMessage());
        }
    }

    /**
     * User Phone
     * 
     * Update Primary Phone
     * 
     * @param bool jsonResponse
     * @param User user
     * @param string phoneNumber
     */
    public function updatePrimaryPhone(
        bool $jsonResponse,
        User $user,
        string $phoneNumber
    )
    {
        try {

            // Find phone
            $phone = EntityUtil::findOnePhone($this->lang, $this->entityManager, $user, $phoneNumber);

            // Exception
            if($phone instanceof \Exception) throw new \Exception($phone->getMessage());
            
            // Find Primary
            $findPrimary = EntityUtil::findPrimaryPhone($this->lang, $this->entityManager, $user);

            // Update as off Primary
            if(!$findPrimary instanceof \Exception) {

                // Turn Off primary
                $findPrimary->setIsPrimary(false);
                $phone->setUpdatedDate(new \DateTime());

                // Flush Changes
                $this->entityManager->flush();
                
            }

            // Add as Primary
            $phone->setIsPrimary(true);
            $phone->setUpdatedDate(new \DateTime());

            // Flush Changes
            $this->entityManager->flush();

            // Return Response
            return ResponseUtil::response($jsonResponse, $phone, 200, self::formatPhoneDetails($phone), $this->lang->trans('account.action.success'));

        } catch (\Exception $th) {
            //throw $th;
            return ResponseUtil::response($jsonResponse, $th, 400, [], $th->getMessage());
        }
    }

    /**
     * User Phone
     * 
     * Remove Phone
     * 
     * @param bool jsonResponse
     * @param User user
     * @param string phoneNumber
     */
    public function removePhone(
        bool $jsonResponse,
        User $user,
        string $phoneNumber
    )
    {
        try {

            // Find phone
            $phone = EntityUtil::findOnePhone($this->lang, $this->entityManager, $user, $phoneNumber);

            // Exception
            if($phone instanceof \Exception) throw new \Exception($phone->getMessage());

            // Check phone if is primary
            if($phone->isPrimary()) throw new \Exception($this->lang->trans('account.phone.no_primary.remove'));

            // Remove & Flush Changes
            $this->entityManager->remove($phone);
            $this->entityManager->flush();

            // Return Response
            return ResponseUtil::response($jsonResponse, $user, 200, [], $this->lang->trans('account.action.success'));

        } catch (\Exception $th) {
            //throw $th;
            return ResponseUtil::response($jsonResponse, $th, 400, [], $th->getMessage());
        }
    }

    /**
     * User Phone
     * 
     * Phone Details
     * 
     * @param bool jsonResponse
     * @param User user
     * @param string phoneNumber
     */
    public function phoneDetails(
        bool $jsonResponse,
        User $user,
        string $phoneNumber
    )
    {
        try {

            // Find phone
            $phone = EntityUtil::findOnePhone($this->lang, $this->entityManager, $user, $phoneNumber);

            // Exception
            if($phone instanceof \Exception) throw new \Exception($phone->getMessage());

            // Return Response
            return ResponseUtil::response($jsonResponse, $phone, 200, self::formatPhoneDetails($phone), $this->lang->trans('account.action.success'));

        } catch (\Exception $th) {
            //throw $th;
            return ResponseUtil::response($jsonResponse, $th, 400, [], $th->getMessage());
        }
    }

    /**
     * Format phone details
     */
    public static function formatPhoneDetails(
        UserPhone $phone
    )
    {
        // Details
        $data = [
            'country' => [
                'name' => $phone->getCountry()->getName(),
                'code' =>  $phone->getCountry()->getCode(),
                'dia_code' =>  $phone->getCountry()->getDialCode()
            ],
            'phone' => $phone->getPhone(),
            'isPrimary' => $phone->isPrimary()
        ];

        // Return Data
        return $data;
    }
}