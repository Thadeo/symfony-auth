<?php
namespace Module\User\Controller;

use App\Component\Controller\ModuleController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends ModuleController{

    #[Route('/user', name: 'app_user_hello')]
    public function index() : Response {
        return $this->moduleview('@Module/user', '/index.html.twig');
    }
}