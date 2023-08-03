<?php
namespace App\Service;

use App\Component\Util\EntityUtil;
use App\Component\Util\FormatUtil;
use App\Component\Util\GenerateUtil;
use App\Component\Util\ResponseUtil;
use App\Entity\Country;
use App\Entity\User;
use App\Entity\UserAddress;
use App\Entity\UserPhone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class AccountService
{
    private $entityManager;
    private $security;

    public function __construct(
        EntityManagerInterface $entityManager,
        private TranslatorInterface $lang,
        SecurityService $security
    )
    {
        $this->entityManager = $entityManager;
        $this->lang = $lang;
        $this->security = $security;
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
     * Gender Type
     * 
     * get All User Gender
     * 
     * @param bool jsonResponse
     */
    public function allGenderType(
        bool $jsonResponse
    )
    {
        try {

            // Find Gender Type
            $genderType = EntityUtil::findAllGenderType($this->lang, $this->entityManager);

            // Exception
            if($genderType instanceof \Exception) throw new \Exception($genderType->getMessage());
            
            // Hold data
            $data = [];

            // Loop Gender Type
            foreach ($genderType as $key => $gender) {
                # code...
                $data[] = [
                    'name' => $gender->getName(),
                    'code' => $gender->getCode()
                ];
            }
            
            // Return Response
            return ResponseUtil::response($jsonResponse, $genderType, 200, $data, $this->lang->trans('role.action.success'));

        } catch (\Exception $th) {
            //throw $th;
            return ResponseUtil::response($jsonResponse, $th, 400, [], $th->getMessage());
        }
    }

    /**
     * User Profile
     * 
     * Update Profile
     * 
     * @param bool jsonResponse
     * @param User user
     * @param string firstname
     * @param string middlename
     * @param string lastname
     * @param string birthDate
     * @param string gender
     * @param string email
     * 
     */
    public function updateProfile(
        bool $jsonResponse,
        User $user,
        string $firtName = null,
        string $middleName = null,
        string $lastName = null,
        string $birthDate = null,
        string $gender = null,
        string $email = null
    )
    {
        try {

            // Nothing to change
            if(empty($firtName) && empty($lastName) && empty($middleName) && empty($email)) throw new \Exception($this->lang->trans('account.no_action.update'));
            
            // Check email if exist
            if($email) {
                // Find email
                $findEmail = EntityUtil::findOneUser($this->lang, $this->entityManager, $email);

                // Exist email
                if(!$findEmail instanceof \Exception) {
                    // Verify if user are not the same
                    if($user !== $findEmail) throw new \Exception($this->lang->trans('account.email.exist'));
                    
                }
            }

            // Check gender if exist
            if($gender) {
                // Find gender
                $findGender = EntityUtil::findOneGenderType($this->lang, $this->entityManager, $gender);

                // Exception
                if($findGender instanceof \Exception) throw new \Exception($findGender->getMessage());
            }
            
            // Update new details
            if($firtName) $user->setFirstName($firtName);
            if($middleName) $user->setMiddleName($middleName);
            if($lastName) $user->setLastName($lastName);
            if($birthDate) $user->setBirthDate(FormatUtil::dateToDateTime($birthDate));
            if($gender) $user->setGender($findGender);
            if($email) $user->setEmail($email);

            // Flush Changes
            $this->entityManager->flush();

            // Return Response
            return ResponseUtil::response($jsonResponse, $user, 200, self::formatProfileDetails($user), $this->lang->trans('account.action.success'));

        } catch (\Exception $th) {
            //throw $th;
            return ResponseUtil::response($jsonResponse, $th, 400, [], $th->getMessage());
        }
    }

    /**
     * User Profile
     * 
     * Profile Details
     * 
     * @param bool jsonResponse
     * @param User user
     * 
     */
    public function profileDetails(
        bool $jsonResponse,
        User $user
    )
    {
        try {

            // Return Response
            return ResponseUtil::response($jsonResponse, $user, 200, self::formatProfileDetails($user), $this->lang->trans('account.action.success'));

        } catch (\Exception $th) {
            //throw $th;
            return ResponseUtil::response($jsonResponse, $th, 400, [], $th->getMessage());
        }
    }

    /**
     * Format profile details
     */
    public static function formatProfileDetails(
        User $user
    )
    {
        // Details
        $data = [
            'first_name' => $user->getFirstName(),
            'last_name' => $user->getLastName(),
            'middle_name' => $user->getMiddleName(),
            'birth_date' => $user->getBirthDate()->format('d/m/Y'),
            'gender' => $user->getGender()->getName()
        ];

        // Return Data
        return $data;
    }

    ####################################### PHONE ######################################
    ######################################################################################

    /**
     * User Phone
     * 
     * Get All Phone
     * 
     * @param bool jsonResponse
     * @param User user
     * @param string search
     * @param string country
     * @param bool isPrimary
     * 
     */
    public function allPhone(
        bool $jsonResponse,
        User $user,
        string $search = null,
        string $country = null,
        bool $isPrimary = null
    )
    {
        try {

            // Find phone
            $phones = EntityUtil::findAllPhone($this->lang, $this->entityManager, $user, $search, $country, $isPrimary);

            // Exception
            if($phones instanceof \Exception) throw new \Exception($phones->getMessage());

            // Hold Data
            $data = [];

            // Loop all phone
            foreach ($phones as $key => $phone) {
                # code...
                $data[] = self::formatPhoneDetails($phone);
            }

            // Return Response
            return ResponseUtil::response($jsonResponse, $phones, 200, $data, $this->lang->trans('account.action.success'));

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
            $phone->setIdentifier(GenerateUtil::number(10));

            // Add Date & Flush Changes
            $this->entityManager->persist($phone);
            $this->entityManager->flush();

            // Set User Primary Phone
            if($isPrimary) {
                // Update User
                $user->setPhone($phone);

                // Flush Changes
                $this->entityManager->flush();
            }
            
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
     * @param string identifier
     * @param string phoneNumber
     */
    public function updatePhone(
        bool $jsonResponse,
        User $user,
        string $country,
        string $identifier,
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
            $phone = EntityUtil::findOnePhone($this->lang, $this->entityManager, $user, $identifier);

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
     * @param string identifier
     */
    public function updatePrimaryPhone(
        bool $jsonResponse,
        User $user,
        string $identifier
    )
    {
        try {

            // Find phone
            $phone = EntityUtil::findOnePhone($this->lang, $this->entityManager, $user, $identifier);

            // Exception
            if($phone instanceof \Exception) throw new \Exception($phone->getMessage());
            
            // Find Primary
            $findPrimary = EntityUtil::findPrimaryPhone($this->lang, $this->entityManager, $user);

            // Update as off Primary
            if(!$findPrimary instanceof \Exception) {

                // Turn Off primary
                $findPrimary->setIsPrimary(false);
                $findPrimary->setUpdatedDate(new \DateTime());

                // Flush Changes
                $this->entityManager->flush();
                
            }

            // Add as Primary
            $phone->setIsPrimary(true);
            $phone->setUpdatedDate(new \DateTime());

            // Flush Changes
            $this->entityManager->flush();

            // Set User Primary Phone
            $user->setPhone($phone);

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
     * @param string identifier
     */
    public function removePhone(
        bool $jsonResponse,
        User $user,
        string $identifier
    )
    {
        try {

            // Find phone
            $phone = EntityUtil::findOnePhone($this->lang, $this->entityManager, $user, $identifier);

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
     * @param string identifier
     */
    public function phoneDetails(
        bool $jsonResponse,
        User $user,
        string $identifier
    )
    {
        try {

            // Find phone
            $phone = EntityUtil::findOnePhone($this->lang, $this->entityManager, $user, $identifier);

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
            'id' => $phone->getIdentifier(),
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

    ####################################### ADDRESS ######################################
    ######################################################################################

    /**
     * User Address
     * 
     * Get All Address
     * 
     * @param bool jsonResponse
     * @param User user
     * @param string search
     * @param string country
     * @param string state
     * @param bool isPrimary
     * 
     */
    public function allAddress(
        bool $jsonResponse,
        User $user,
        string $search = null,
        string $country = null,
        string $state = null,
        bool $isPrimary = null
    )
    {
        try {

            // Find address
            $addresses = EntityUtil::findAllAddress($this->lang, $this->entityManager, $user, $search, $country, $state, $isPrimary);

            // Exception
            if($addresses instanceof \Exception) throw new \Exception($addresses->getMessage());

            // Hold Data
            $data = [];

            // Loop all address
            foreach ($addresses as $key => $address) {
                # code...
                $data[] = self::formatAddressDetails($address);
            }

            // Return Response
            return ResponseUtil::response($jsonResponse, $addresses, 200, $data, $this->lang->trans('account.action.success'));

        } catch (\Exception $th) {
            //throw $th;
            return ResponseUtil::response($jsonResponse, $th, 400, [], $th->getMessage());
        }
    }

    /**
     * User Address
     * 
     * Add Address
     * 
     * @param bool jsonResponse
     * @param User user
     * @param Country country
     * @param string state
     * @param string city
     * @param string address
     * @param string addressOptional
     * @param string postalCode
     */
    public function addAddress(
        bool $jsonResponse,
        User $user,
        Country $country,
        string $state,
        string $city,
        string $address1,
        string $addressOptional = null,
        string $postalCode
    )
    {
        try {

            // Find Country State
            $countryState = EntityUtil::findOneCountryState($this->lang, $this->entityManager, $country->getCode(), $state);

            // Exception
            if($countryState instanceof \Exception) throw new \Exception($countryState->getMessage());

            // Find Address if exist
            $addressExist = EntityUtil::findOneAddressByPostalCode($this->lang, $this->entityManager, $user, $postalCode);

            // Exception
            if(!$addressExist instanceof \Exception) throw new \Exception($this->lang->trans('account.address.exist'));

            // Find Primary
            $findPrimary = EntityUtil::findPrimaryAddress($this->lang, $this->entityManager, $user);

            // Is Primary
            $isPrimary = ($findPrimary instanceof \Exception) ? true : false;

            // Prepaired DB
            $address = new UserAddress();
            $address->setUser($user);
            $address->setDate(new \DateTime());
            $address->setCountry($country);
            $address->setState($countryState);
            $address->setCity($city);
            $address->setAddress($address1);
            $address->setAddress2($addressOptional);
            $address->setPostalCode($postalCode);
            $address->setIsPrimary($isPrimary);
            $address->setIsVerified(false);
            $address->setActive(true);
            $address->setUpdatedDate(new \DateTime());
            $address->setIdentifier(GenerateUtil::number(10));

            // Add Date & Flush Changes
            $this->entityManager->persist($address);
            $this->entityManager->flush();

            // Set User Primary Address
            if($isPrimary) {
                // Update User
                $user->setAddress($address);

                // Flush Changes
                $this->entityManager->flush();
            }
            
            // Return Response
            return ResponseUtil::response($jsonResponse, $address, 200, self::formatAddressDetails($address), $this->lang->trans('account.action.success'));

        } catch (\Exception $th) {
            //throw $th;
            return ResponseUtil::response($jsonResponse, $th, 400, [], $th->getMessage());
        }
    }

    /**
     * User Address
     * 
     * Update Address
     * 
     * @param bool jsonResponse
     * @param User user
     * @param string identifier
     * @param Country country
     * @param string state
     * @param string city
     * @param string address
     * @param string addressOptional
     * @param string postalCode
     * 
     */
    public function updateAddress(
        bool $jsonResponse,
        User $user,
        string $identifier,
        Country $country,
        string $state,
        string $city,
        string $address1,
        string $addressOptional = null,
        string $postalCode
    )
    {
        try {

            // Find address
            $address = EntityUtil::findOneAddress($this->lang, $this->entityManager, $user, $identifier);

            // Address not Exist
            if($address instanceof \Exception) throw new \Exception($address->getMessage());

            // Find Address if exist
            $addressExist = EntityUtil::findOneAddressByPostalCode($this->lang, $this->entityManager, $user, $postalCode);

            // Exception
            if(!$addressExist instanceof \Exception) throw new \Exception($this->lang->trans('account.address.exist'));

            // Find Country State
            $countryState = EntityUtil::findOneCountryState($this->lang, $this->entityManager, $country->getCode(), $state);

            // Exception
            if($countryState instanceof \Exception) throw new \Exception($countryState->getMessage());
            
            // Update address
            $address->setCountry($country);
            $address->setState($countryState);
            $address->setCity($city);
            $address->setAddress($address1);
            $address->setAddress2($addressOptional);
            $address->setPostalCode($postalCode);
            $address->setUpdatedDate(new \DateTime());

            // Flush Changes
            $this->entityManager->flush();

            // Return Response
            return ResponseUtil::response($jsonResponse, $address, 200, self::formatAddressDetails($address), $this->lang->trans('account.action.success'));

        } catch (\Exception $th) {
            //throw $th;
            return ResponseUtil::response($jsonResponse, $th, 400, [], $th->getMessage());
        }
    }

    /**
     * User Address
     * 
     * Update Primary Address
     * 
     * @param bool jsonResponse
     * @param User user
     * @param string identifier
     */
    public function updatePrimaryAddress(
        bool $jsonResponse,
        User $user,
        string $identifier
    )
    {
        try {

            // Find address
            $address = EntityUtil::findOneAddress($this->lang, $this->entityManager, $user, $identifier);

            // Exception
            if($address instanceof \Exception) throw new \Exception($address->getMessage());
            
            // Find Primary
            $findPrimary = EntityUtil::findPrimaryAddress($this->lang, $this->entityManager, $user);

            // Update as off Primary
            if(!$findPrimary instanceof \Exception) {

                // Turn Off primary
                $findPrimary->setIsPrimary(false);
                $findPrimary->setUpdatedDate(new \DateTime());

                // Flush Changes
                $this->entityManager->flush();
                
            }

            // Add as Primary
            $address->setIsPrimary(true);
            $address->setUpdatedDate(new \DateTime());

            // Flush Changes
            $this->entityManager->flush();

            // Set User Primary Address
            $user->setAddress($address);

            // Flush Changes
            $this->entityManager->flush();

            // Return Response
            return ResponseUtil::response($jsonResponse, $address, 200, self::formatAddressDetails($address), $this->lang->trans('account.action.success'));

        } catch (\Exception $th) {
            //throw $th;
            return ResponseUtil::response($jsonResponse, $th, 400, [], $th->getMessage());
        }
    }

    /**
     * User Address
     * 
     * Remove Address
     * 
     * @param bool jsonResponse
     * @param User user
     * @param string identifier
     */
    public function removeAddress(
        bool $jsonResponse,
        User $user,
        string $identifier
    )
    {
        try {

            // Find address
            $address = EntityUtil::findOneAddress($this->lang, $this->entityManager, $user, $identifier);

            // Exception
            if($address instanceof \Exception) throw new \Exception($address->getMessage());

            // Check address if is primary
            if($address->isPrimary()) throw new \Exception($this->lang->trans('account.address.no_primary.remove'));

            // Remove & Flush Changes
            $this->entityManager->remove($address);
            $this->entityManager->flush();

            // Return Response
            return ResponseUtil::response($jsonResponse, $user, 200, [], $this->lang->trans('account.action.success'));

        } catch (\Exception $th) {
            //throw $th;
            return ResponseUtil::response($jsonResponse, $th, 400, [], $th->getMessage());
        }
    }

    /**
     * User Address
     * 
     * Address Details
     * 
     * @param bool jsonResponse
     * @param User user
     * @param string identifier
     */
    public function addressDetails(
        bool $jsonResponse,
        User $user,
        string $identifier
    )
    {
        try {

            // Find address
            $address = EntityUtil::findOneAddress($this->lang, $this->entityManager, $user, $identifier);

            // Exception
            if($address instanceof \Exception) throw new \Exception($address->getMessage());

            // Return Response
            return ResponseUtil::response($jsonResponse, $address, 200, self::formatAddressDetails($address), $this->lang->trans('account.action.success'));

        } catch (\Exception $th) {
            //throw $th;
            return ResponseUtil::response($jsonResponse, $th, 400, [], $th->getMessage());
        }
    }

    /**
     * Format address details
     */
    public static function formatAddressDetails(
        UserAddress $address
    )
    {
        // Details
        $data = [
            'id' => $address->getIdentifier(),
            'country' => [
                'name' => $address->getCountry()->getName(),
                'code' =>  $address->getCountry()->getCode()
            ],
            'state' => [
                'name' => $address->getState()->getName(),
                'code' =>  $address->getState()->getCode()
            ],
            'address' => $address->getAddress(),
            'address_2' => $address->getAddress2(),
            'postal_code' => $address->getPostalCode(),
            'isPrimary' => $address->isPrimary()
        ];

        // Return Data
        return $data;
    }

    
    ####################################### SECURITY ######################################
    ######################################################################################


    /**
     * Change Password
     * 
     * update password
     * 
     * @param bool jsonResponse
     * @param User user
     * @param string oldPassword
     * @param string newPassword
     */
    public function updatePassword(
        bool $jsonResponse,
        User $user,
        string $oldPassword,
        string $newPassword
    )
    {
        try {
            
            // Verify current password
            $verifyUserByPassword = $this->security->userNewOldPasswordMatch($user, $oldPassword, $newPassword);

            // Exception
            if($verifyUserByPassword instanceof \Exception) throw new \Exception($verifyUserByPassword->getMessage());
            

            // Hash new Password
            $passwordHash = $this->security->userPasswordHash($user, $newPassword);

            // Exception
            if($passwordHash instanceof \Exception) throw new \Exception($passwordHash->getMessage());

            // Update Data
            $user->setPassword($passwordHash);

            // Flush changes
            $this->entityManager->flush();

            // Add Activity
            $this->security->addUserActivity($user, 'auth_reset_password', 'Change Password', $user->getMode());

            // Return Response
            return ResponseUtil::response($jsonResponse, $user, 200, ['user' => $user->getFullName(), 'email' => $user->getEmail()], $this->lang->trans('account.action.success'));

        } catch (\Exception $th) {
            //throw $th;
            return ResponseUtil::response($jsonResponse, $th, 400, null, $th->getMessage());
        }
    }
}