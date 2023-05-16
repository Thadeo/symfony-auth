<?php
namespace Module\User\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController{

    #[Route('/user', name: 'app_user_hello')]
    public function index() : Response {
        return $this->render('@Module/user/templates/index.html.twig');
    }
}