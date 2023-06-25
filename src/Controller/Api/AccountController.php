<?php

namespace App\Controller\Api;

use App\Component\Controller\BaseController;
use App\Component\Request\AppRequest;
use App\Service\AccountService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends BaseController
{
    /**
     * All Phone
     * 
     * Get all Phone
     */
    #[Route('/api/account/all/phone', name: 'api_account_all_phone', methods: ['POST'])]
    public function allPhone(
        AppRequest $request,
        AccountService $account
    ): Response
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
    ): Response
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
    #[Route('/api/account/update/phone/{phonenumber}', name: 'api_account_update_phone', methods: ['POST'])]
    public function updatePhone(
        AppRequest $request,
        AccountService $account,
        string $phonenumber
    ): Response
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
                        $phonenumber,
                        $validate['phone']);

        // Return Response
        return $this->appJson($phone);
    }

    /**
     * Set Phone Primary
     * 
     * update primary phone
     */
    #[Route('/api/account/primary/phone/{phonenumber}', name: 'api_account_set_primary_phone', methods: ['GET'])]
    public function setPrimaryPhone(
        AccountService $account,
        string $phonenumber
    ): Response
    {
        // Phone
        $phone = $account->updatePrimaryPhone(true, $this->getUser(), $phonenumber);

        // Return Response
        return $this->appJson($phone);
    }

    /**
     * Phone Details
     * 
     * remove phone
     */
    #[Route('/api/account/remove/phone/{phonenumber}', name: 'api_account_remove_phone', methods: ['GET', 'DELETE'])]
    public function removePhone(
        AccountService $account,
        string $phonenumber
    ): Response
    {
        // Phone
        $phone = $account->removePhone(true, $this->getUser(), $phonenumber);

        // Return Response
        return $this->appJson($phone);
    }

    /**
     * Phone Details
     * 
     * get phone details
     */
    #[Route('/api/account/find/phone/{phonenumber}', name: 'api_account_phone_details', methods: ['GET'])]
    public function phoneDetails(
        AccountService $account,
        string $phonenumber
    ): Response
    {
        // Phone
        $phone = $account->phoneDetails(true, $this->getUser(), $phonenumber);

        // Return Response
        return $this->appJson($phone);
    }
}
