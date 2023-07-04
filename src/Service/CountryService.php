<?php
namespace App\Service;

use App\Component\Util\EntityUtil;
use App\Component\Util\ResponseUtil;
use App\Entity\Country;
use App\Entity\CountryState;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class CountryService
{
    private $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        private TranslatorInterface $lang,
        private SettingService $setting,
    )
    {
        $this->entityManager = $entityManager;
        $this->lang = $lang;
        $this->setting = $setting;
    }

    /**
     * Country
     * 
     * Get all country
     * 
     * @param bool jsonResponse
     * @param string country
     * @param string page
     * @param string perPage
     * @param string orderBy
     * @param string orderColumn
     */
    public function allCountry(
        bool $jsonResponse,
        string $country = null,
        int $page = null,
        int $perPage = null,
        string $orderBy = null,
        string $orderColumn = null
    )
    {
        try {
            
            // Find Countries
            $countries = EntityUtil::findAllCountry($this->lang, $this->entityManager, $country, ($page) ? $page : 1, ($perPage) ? $perPage : 10, $orderBy, $orderColumn);

            // Exception
            if($countries instanceof \Exception) throw new \Exception($countries->getMessage());

            // Get Total Page
            $totalPage = ceil($countries['count'] / $perPage);

            // Hold data
             $data = [
                'data' => [],
                'pagination' => [
                    'total_data' => $countries['count'],
                    'total_page' => $totalPage,
                    'page' => $page,
                    'per_page' => $perPage,
                    'next_page' => ($perPage > $countries['count']) ? 0 : $page + 1,
                    'prev_page' => $page - 1
                ]
             ];

             // Loop Countries
             foreach ($countries['data'] as $key => $country) {
                 # code...
                 $data['data'][] = self::formatCountryDetails($country);
             }

            // Return Response
            return ResponseUtil::response($jsonResponse, $countries['data'], 200, $data, $this->lang->trans('country.action.success'));

        } catch (\Exception $th) {
            //throw $th;
            return ResponseUtil::response($jsonResponse, $th, 400, null, $th->getMessage());
        }
    }

    /**
     * Country
     * 
     * Get one country
     * 
     * @param bool jsonResponse
     * @param string country
     */
    public function country(
        bool $jsonResponse,
        string $country
    )
    {
        try {
            
            // Find Country
            $country = EntityUtil::findOneCountry($this->lang, $this->entityManager, $country);

            // Exception
            if($country instanceof \Exception) throw new \Exception($country->getMessage());

            // Return Response
            return ResponseUtil::response($jsonResponse, $country, 200, self::formatCountryDetails($country), $this->lang->trans('country.action.success'));

        } catch (\Exception $th) {
            //throw $th;
            return ResponseUtil::response($jsonResponse, $th, 400, null, $th->getMessage());
        }
    }

    /**
     * Format country details
     */
    public static function formatCountryDetails(
        Country $country
    )
    {
        // Details
        $data = [
            'name' => $country->getName(),
            'code' => $country->getCode(),
            'captal' => $country->getCapital(),
            'dial_code' => $country->getDialCode()
        ];

        // Return Data
        return $data;
    }
    
    /**
     * Country State
     * 
     * Get all state
     * 
     * @param bool jsonResponse
     * @param string country
     * @param string page
     * @param string perPage
     * @param string orderBy
     * @param string orderColumn
     */
    public function allState(
        bool $jsonResponse,
        string $country,
        string $state = null,
        int $page = null,
        int $perPage = null,
        string $orderBy = null,
        string $orderColumn = null
    )
    {
        try {
            
            // Find States
            $states = EntityUtil::findAllCountryState($this->lang, $this->entityManager, $country, $state, ($page) ? $page : 1, ($perPage) ? $perPage : 10, $orderBy, $orderColumn);

            // Exception
            if($states instanceof \Exception) throw new \Exception($states->getMessage());

            // Get Total Page
            $totalPage = ceil($states['count'] / $perPage);

            // Hold data
             $data = [
                'data' => [],
                'pagination' => [
                    'total_data' => $states['count'],
                    'total_page' => $totalPage,
                    'page' => $page,
                    'per_page' => $perPage,
                    'next_page' => ($perPage > $states['count']) ? 0 : $page + 1,
                    'prev_page' => $page - 1
                ]
             ];

             // Loop State
             foreach ($states['data'] as $key => $state) {
                 # code...
                 $data['data'][] = self::formatStateDetails($state);
             }

            // Return Response
            return ResponseUtil::response($jsonResponse, $states['data'], 200, $data, $this->lang->trans('country.action.success'));

        } catch (\Exception $th) {
            //throw $th;
            return ResponseUtil::response($jsonResponse, $th, 400, null, $th->getMessage());
        }
    }

    /**
     * State
     * 
     * Get one state
     * 
     * @param bool jsonResponse
     * @param string country
     * @param string state
     */
    public function state(
        bool $jsonResponse,
        string $country,
        string $state
    )
    {
        try {
            
            // Find State
            $state = EntityUtil::findOneCountryState($this->lang, $this->entityManager, $country, $state);

            // Exception
            if($state instanceof \Exception) throw new \Exception($state->getMessage());

            // Return Response
            return ResponseUtil::response($jsonResponse, $state, 200, self::formatStateDetails($state), $this->lang->trans('country.action.success'));

        } catch (\Exception $th) {
            //throw $th;
            return ResponseUtil::response($jsonResponse, $th, 400, null, $th->getMessage());
        }
    }

    /**
     * Format state details
     */
    public static function formatStateDetails(
        CountryState $state
    )
    {
        // Details
        $data = [
            'name' => $state->getName(),
            'code' => $state->getCode()
        ];

        // Return Data
        return $data;
    }

}