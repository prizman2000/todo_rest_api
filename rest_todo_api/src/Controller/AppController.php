<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AppController extends AbstractController
{
    public function response($data, $status = Response::HTTP_OK, $headers = []): JsonResponse
    {
        return new JsonResponse($data, $status, $headers);
    }

    public function transformJsonBody(Request $request): Request
    {
        $data = json_decode($request->getContent(), true);
        if (null === $data) {
            return $request;
        }
        $request->request->replace($data);

        return $request;
    }
}
