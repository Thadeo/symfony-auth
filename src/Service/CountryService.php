<?php
namespace App\Service;

use App\Component\Util\EntityUtil;
use App\Component\Util\ResponseUtil;
use App\Entity\Country;
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
     */
    public function country(
        bool $jsonResponse,
        string $country = null,
        int $page = 1,
        int $perPage = 10,
        string $orderBy = 'desc'
    )
    {
        try {
            
            // Find Countries
            $countries = EntityUtil::findAllCountry($this->lang, $this->entityManager, $country, $page, $perPage, $orderBy);

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
            return ResponseUtil::response($jsonResponse, $countries['data'], 200, $data, $this->lang->trans('misc.action.success'));

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
}