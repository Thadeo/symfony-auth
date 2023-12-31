<?php
namespace App\Component\Controller;

use App\Service\SettingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Module Controller
 * 
 * It uses for backend controller with full authentication pass
 */
class ModuleController extends AbstractController
{
    private $tokenStorage;
    private $setting;
    
    public function __construct(
        TokenStorageInterface $tokenStorage,
        SettingService $setting
    )
    {
        $this->tokenStorage = $tokenStorage;
        $this->setting = $setting;

        // Check Authentication
        if($this->tokenStorage->getToken() == null) {
            throw new \Exception("Authentication is required");
        }
    }

    /**
     * Renders a view
     * 
     * If an invalid form is found in the list of parameters, a 422 status code is returned.
     * Forms found in parameters are auto-cast to form views.
     */
    protected function moduleview(
        string $modulePath,
        string $view,
        array $parameters = [],
        Response $response = null
    ): Response {
        
        // Add more data
        $parameters['user'] = $this->tokenStorage->getToken()->getUser();

        // Render the template
        return $this->render($modulePath.'/templates'.$view, $parameters);
    }
}