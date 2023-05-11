<?php

namespace App\EventSubscriber;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionEventSubscriber implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $event): void
    {
        // Exception & request
        $exception = $event->getThrowable();
        $request = $event->getRequest();

        // Get status code
        $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
        if ($exception instanceof HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();
        }

        // Json Response
        $responseData = [
                'status' => self::formatStatusCode($statusCode),
                'message' => $exception->getMessage()
        ];

        // Custom path and show response
        if(in_array(str_replace('/', '', substr($request->getRequestUri(), 0, 4)), ['api'])) {
            $response = new JsonResponse($responseData, $statusCode);
            $event->setResponse($response);
        }
    }

    private static function formatStatusCode($statusCode) {
        switch ($statusCode) {
            case 0: case '0':
                # 400
                $code = 404;
                break;
            
            default:
                # code...
                $code = $statusCode;
                break;
        }

        // Return key
        return $code;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }
}
