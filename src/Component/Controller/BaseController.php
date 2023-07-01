<?php
namespace App\Component\Controller;

use App\Service\SettingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Base Controller
 * 
 * It uses for non authentication controller
 * Not compatable for modules
 */
class BaseController extends AbstractController
{
    private $setting;
    
    public function __construct(
        SettingService $setting
    )
    {
        $this->setting = $setting;
    }

    /**
     * Renders a view
     * 
     * If an invalid form is found in the list of parameters, a 422 status code is returned.
     * Forms found in parameters are auto-cast to form views.
     */
    protected function appview(
        string $view,
        array $parameters = [],
        Response $response = null
    ): Response {

        // Find Template
        $template = $this->setting->getValueByKey('template');

        // Verify template
        if(empty($template)) $template = 'default';

        // Render the template
        return $this->render($template.'/'.$view, $parameters);
    }

    /**
     * Returns a JsonResponse that uses the serializer component if enabled, or json_encode.
     *
     * @param array data
     * @param int status
     */
    protected function appJson(array $data, int $status = null)
    {
        return $this->json($data, ($status) ? $status : $data['status']);
    }
}