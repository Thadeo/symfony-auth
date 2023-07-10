<?php

namespace App\Controller\Api;

use App\Component\Controller\BaseController;
use App\Component\Request\AppRequest;
use App\Service\AccountService;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends BaseController
{
    /**
     * Update Profile
     * 
     * profile
     */
    #[Route('/api/account/update/profile', name: 'api_account_update_profile', methods: ['POST'])]
    public function updateProfile(
        AppRequest $request,
        AccountService $account
    )
    {
        $validate = $request->validate([
            'first_name' => 'required|string|min:4',
            'middle_name' => 'string|min:4',
            'last_name' => 'string|min:4',
            'email' => 'email'
        ]);

        // Verify Validation
        if(!empty($validate['errors'])) return $this->json($validate, 400);
        
        // Profile
        $profile = $account->updateProfile(true, $this->getUser(),
                        $validate['first_name'],
                        $validate['middle_name'],
                        $validate['last_name'],
                        $validate['email']);

        // Return Response
        return $this->appJson($profile);
    }

    /**
     * Profile Details
     * 
     * profile
     */
    #[Route('/api/account/find/profile', name: 'api_account_find_profile', methods: ['GET'])]
    public function profileDetails(
        AccountService $account
    )
    {
        // Profile
        $profile = $account->profileDetails(true, $this->getUser());

        // Return Response
        return $this->appJson($profile);
    }

    /**
     * All Phone
     * 
     * Get all Phone
     */
    #[Route('/api/account/all/phone', name: 'api_account_all_phone', methods: ['POST'])]
    public function allPhone(
        AppRequest $request,
        AccountService $account
    )
    {
        $validate = $request->validate([
            'search' => 'string',
            'country' => 'string',
            'isPrimary' => 'bool'
        ]);

        // Verify Validation
        if(!empty($validate['errors'])) return $this->json($validate, 400);
        
        // Phone
        $phone = $account->allPhone(true, $this->getUser(), $validate['search'], $validate['country'], $validate['isPrimary']);

        // Return Response
        return $this->appJson($phone);
    }

    /**
     * Add Phone
     * 
     * phone number
     */
    #[Route('/api/account/add/phone', name: 'api_account_add_phone', methods: ['POST'])]
    public function addPhone(
        AppRequest $request,
        AccountService $account
    )
    {
        $validate = $request->validate([
            'country_code' => 'required|string',
            'phone' => 'required|numeric'
        ]);

        // Verify Validation
        if(!empty($validate['errors'])) return $this->json($validate, 400);
        
        // Phone
        $phone = $account->addPhone(true, $this->getUser(),
                        $validate['country_code'],
                        $validate['phone']);

        // Return Response
        return $this->appJson($phone);
    }

    /**
     * Update Phone
     * 
     * phone number
     */
    #[Route('/api/account/update/phone/{identifier}', name: 'api_account_update_phone', methods: ['POST'])]
    public function updatePhone(
        AppRequest $request,
        AccountService $account,
        string $identifier
    )
    {
        $validate = $request->validate([
            'country_code' => 'required|string',
            'phone' => 'required|numeric'
        ]);

        // Verify Validation
        if(!empty($validate['errors'])) return $this->json($validate, 400);
        
        // Phone
        $phone = $account->updatePhone(true, $this->getUser(),
                        $validate['country_code'],
                        $identifier,
                        $validate['phone']);

        // Return Response
        return $this->appJson($phone);
    }

    /**
     * Set Phone Primary
     * 
     * update primary phone
     */
    #[Route('/api/account/primary/phone/{identifier}', name: 'api_account_set_primary_phone', methods: ['GET'])]
    public function setPrimaryPhone(
        AccountService $account,
        string $identifier
    )
    {
        // Phone
        $phone = $account->updatePrimaryPhone(true, $this->getUser(), $identifier);

        // Return Response
        return $this->appJson($phone);
    }

    /**
     * Phone Details
     * 
     * remove phone
     */
    #[Route('/api/account/remove/phone/{identifier}', name: 'api_account_remove_phone', methods: ['GET', 'DELETE'])]
    public function removePhone(
        AccountService $account,
        string $identifier
    )
    {
        // Phone
        $phone = $account->removePhone(true, $this->getUser(), $identifier);

        // Return Response
        return $this->appJson($phone);
    }

    /**
     * Phone Details
     * 
     * get phone details
     */
    #[Route('/api/account/find/phone/{identifier}', name: 'api_account_phone_details', methods: ['GET'])]
    public function phoneDetails(
        AccountService $account,
        string $identifier
    )
    {
        // Phone
        $phone = $account->phoneDetails(true, $this->getUser(), $identifier);

        // Return Response
        return $this->appJson($phone);
    }

    /**
     * All Address
     * 
     * Get all Address
     */
    #[Route('/api/account/all/address', name: 'api_account_all_address', methods: ['POST'])]
    public function allAddress(
        AppRequest $request,
        AccountService $account
    )
    {
        $validate = $request->validate([
            'search' => 'string',
            'country' => 'string',
            'state' => 'string',
            'isPrimary' => 'bool'
        ]);

        // Verify Validation
        if(!empty($validate['errors'])) return $this->json($validate, 400);
        
        // Phone
        $phone = $account->allAddress(true, $this->getUser(), $validate['search'], $validate['country'], $validate['state'], $validate['isPrimary']);

        // Return Response
        return $this->appJson($phone);
    }

    /**
     * Add Address
     * 
     * address
     */
    #[Route('/api/account/add/address', name: 'api_account_add_address', methods: ['POST'])]
    public function addAddress(
        AppRequest $request,
        AccountService $account
    )
    {
        $validate = $request->validate([
            'state' => 'required|string',
            'city' => 'string',
            'address' => 'required|string',
            'address_2' => 'string',
            'postal_code' => 'required|numeric'
        ]);

        // Verify Validation
        if(!empty($validate['errors'])) return $this->json($validate, 400);
        
        // Address
        $address = $account->addAddress(true, $this->getUser(),
                        $this->getUser()->getCountry(),
                        $validate['state'],
                        $validate['city'],
                        $validate['address'],
                        $validate['address_2'],
                        $validate['postal_code']);

        // Return Response
        return $this->appJson($address);
    }

    /**
     * Update Address
     * 
     * address
     */
    #[Route('/api/account/update/address/{identifier}', name: 'api_account_update_address', methods: ['POST'])]
    public function updateAddress(
        AppRequest $request,
        AccountService $account,
        string $identifier
    )
    {
        $validate = $request->validate([
            'state' => 'required|string',
            'city' => 'string',
            'address' => 'required|string',
            'address_2' => 'string',
            'postal_code' => 'required|numeric'
        ]);

        // Verify Validation
        if(!empty($validate['errors'])) return $this->json($validate, 400);
        
        // Address
        $address = $account->updateAddress(true, $this->getUser(),
                        $identifier,
                        $this->getUser()->getCountry(),
                        $validate['state'],
                        $validate['city'],
                        $validate['address'],
                        $validate['address_2'],
                        $validate['postal_code']);

        // Return Response
        return $this->appJson($address);
    }

    /**
     * Set Address Primary
     * 
     * update primary address
     */
    #[Route('/api/account/primary/address/{identifier}', name: 'api_account_set_primary_address', methods: ['GET'])]
    public function setPrimaryAddress(
        AccountService $account,
        string $identifier
    )
    {
        // Address
        $address = $account->updatePrimaryAddress(true, $this->getUser(), $identifier);

        // Return Response
        return $this->appJson($address);
    }

    /**
     * Address Details
     * 
     * remove address
     */
    #[Route('/api/account/remove/address/{identifier}', name: 'api_account_remove_address', methods: ['GET', 'DELETE'])]
    public function removeAddress(
        AccountService $account,
        string $identifier
    )
    {
        // Address
        $address = $account->removeAddress(true, $this->getUser(), $identifier);

        // Return Response
        return $this->appJson($address);
    }

    /**
     * Address Details
     * 
     * get address details
     */
    #[Route('/api/account/find/address/{identifier}', name: 'api_account_address_details', methods: ['GET'])]
    public function addressDetails(
        AccountService $account,
        string $identifier
    )
    {
        // Address
        $address = $account->addressDetails(true, $this->getUser(), $identifier);

        // Return Response
        return $this->appJson($address);
    }

    /**
     * Update Password
     * 
     * password
     */
    #[Route('/api/account/update/password', name: 'api_account_update_password', methods: ['POST'])]
    public function updatePassword(
        AppRequest $request,
        AccountService $account
    )
    {
        $validate = $request->validate([
            'password' => 'required|string',
            'new_password' => 'required|string'
        ]);

        // Verify Validation
        if(!empty($validate['errors'])) return $this->json($validate, 400);
        
        // Password
        $password = $account->updatePassword(true, $this->getUser(),
                        $validate['password'],
                        $validate['new_password']);

        // Return Response
        return $this->appJson($password);
    }
}
