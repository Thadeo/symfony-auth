<?php

namespace App\Controller\Api;

use App\Component\Controller\BaseController;
use App\Component\Request\AppRequest;
use App\Service\AccountService;
use App\Service\CountryService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class PublicDataController extends BaseController
{
    /**
     * Account Type
     * 
     * Fetch Account Type
     */
    #[Route('/api/data/account-type', name: 'api_data_account_type', methods: ['GET'])]
    public function fetchAccountType(
        AccountService $account
    ): Response
    {
        // Fetch Account
        $data = $account->allAccountType(true);

        // Return Response
        return $this->appJson($data);
    }

    /**
     * Country
     * 
     * get all country
     */
    #[Route('/api/data/country', name: 'api_misc_country', methods: ['POST'])]
    public function updateProfile(
        AppRequest $request,
        CountryService $countryService
    ): Response
    {
        $validate = $request->validate([
            'country' => 'string',
            'page' => 'numeric|min:1',
            'per_page' => 'numeric|min:1|max:200',
            'order_by' => 'string'
        ]);

        // Verify Validation
        if(!empty($validate['errors'])) return $this->appJson($validate, 400);
        
        // Country
        $country = $countryService->country(true,
                        $validate['country'],
                        $validate['page'],
                        $validate['per_page'],
                        $validate['order_by']);

        // Return Response
        return $this->appJson($country);
    }
}
