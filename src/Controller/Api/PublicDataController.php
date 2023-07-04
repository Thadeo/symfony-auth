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
    #[Route('/api/data/country', name: 'api_data_country', methods: ['POST'])]
    public function allCountry(
        AppRequest $request,
        CountryService $country
    ): Response
    {
        $validate = $request->validate([
            'country' => 'string',
            'page' => 'numeric|min:1',
            'per_page' => 'numeric|min:1|max:200',
            'order_by' => 'string',
            'order_column' => 'string|match:[id,name]'
        ]);

        // Verify Validation
        if(!empty($validate['errors'])) return $this->appJson($validate, 400);
        
        // Country
        $countries = $country->allCountry(true,
                        $validate['country'],
                        $validate['page'],
                        $validate['per_page'],
                        $validate['order_by'],
                        $validate['order_column']);

        // Return Response
        return $this->appJson($countries);
    }

    /**
     * Country
     * 
     * get all country
     */
    #[Route('/api/data/country/{getCountry}', name: 'api_data_country_details', methods: ['GET'])]
    public function country(
        AppRequest $request,
        CountryService $country,
        string $getCountry
    ): Response
    {
        // Country
        $country = $country->country(true, $getCountry);

        // Return Response
        return $this->appJson($country);
    }

    /**
     * Country State
     * 
     * get all country state
     */
    #[Route('/api/data/country/{getCountry}/state', name: 'api_data_country_state', methods: ['POST'])]
    public function allCountryState(
        AppRequest $request,
        CountryService $country,
        string $getCountry
    ): Response
    {
        $validate = $request->validate([
            'state' => 'string',
            'page' => 'numeric|min:1',
            'per_page' => 'numeric|min:1|max:200',
            'order_by' => 'string',
            'order_column' => 'string|match:[id,name]'
        ]);

        // Verify Validation
        if(!empty($validate['errors'])) return $this->appJson($validate, 400);
        
        // Country States
        $states = $country->allState(true,
                        $getCountry,
                        $validate['state'],
                        $validate['page'],
                        $validate['per_page'],
                        $validate['order_by'],
                        $validate['order_column']);

        // Return Response
        return $this->appJson($states);
    }

    /**
     * Country
     * 
     * get all country
     */
    #[Route('/api/data/country/{getCountry}/state/{getState}', name: 'api_data_country_state_details', methods: ['GET'])]
    public function countryState(
        AppRequest $request,
        CountryService $country,
        string $getCountry,
        string $getState
    ): Response
    {
        // Country State
        $state = $country->state(true, $getCountry, $getState);

        // Return Response
        return $this->appJson($state);
    }
}
