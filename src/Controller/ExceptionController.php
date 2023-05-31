<?php

namespace App\Controller;

use App\Exception\ApiException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExceptionController extends AbstractController
{
    public function onException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        $error = ['error' => $exception->getMessage()];
        // Manejo de excepciones personalizadas
        if ($exception instanceof ApiException) {
            if (!empty($exception->getErrorList())) {
                $error['error_list'] = $exception->getErrorList();
            }

            $response = new JsonResponse($error, $exception->getStatusCode());
            $event->setResponse($response);
            return;
        }

        // Manejo de excepciones por defecto de Symfony
        if ($exception instanceof NotFoundHttpException) {
            $response = new JsonResponse($error, Response::HTTP_NOT_FOUND);
            $event->setResponse($response);
            return;
        }

        // Otras excepciones no manejadas
        $response = new JsonResponse($error, Response::HTTP_INTERNAL_SERVER_ERROR);
        $event->setResponse($response);
    }
}
