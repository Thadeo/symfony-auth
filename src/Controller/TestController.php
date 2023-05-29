<?php

namespace App\Controller;

use App\Component\Validation\RequestValidation;
use App\Service\ModuleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/test', name: 'app_test')]
    public function index(): Response
    {
        return $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }

    #[Route('/test/module-upload', name: 'app_test_module_upload', methods: ['POST'])]
    public function uploadModule(
        Request $request,
        ModuleService $moduleService
    ): Response
    {

       try {
            // Push file to upload
            $uploadFile = $moduleService->uploadModuleFile($request);

            // Exception
            if($uploadFile instanceof \Exception) throw new \Exception($uploadFile->getMessage());
            
            // Successful
            return new Response('Module uploaded successfully');

        } catch (\Exception $th) {
            //throw $th;
            return new Response($th->getMessage());
        }
    }

    #[Route('/test/module', name: 'app_test_module_list')]
    public function listModules(
        ModuleService $moduleService
    ): Response
    {
        $modules = $moduleService->listModules();
        return new Response(json_encode($modules), Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }
    
    #[Route('/api/user', name: 'api_user')]
    public function apiUser(
        ModuleService $moduleService
    ): Response
    {
        $modules = $moduleService->listModules();
        return new Response(json_encode($modules), Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    #[Route('/test/validate', name: 'app_test_validate')]
    public function testValidate(
        Request $request,
        RequestValidation $validation
    ): Response
    {
        /*$validate = $validation->validate(json_decode($request->getContent(), true), [
            'name' => 'required|string|min:100|max:150',
            'good' => 'required|numeric|min:100|max:150',
            'last' => 'required|numeric|min:100|max:150'
        ]);*/

        $validate = $validation->validate(json_decode($request->getContent(), true), [
            'name' => [
                'min' => 10
            ],
            'good' => [
                'min' => 10
            ],
            'last' => [
                'min' => 10
            ]
        ]);
        
        return new Response(json_encode($validate), Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }
}
