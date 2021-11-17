<?php

namespace App\Controller;

use App\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    private $logger;
    private $hasher;

    public function __construct(LoggerInterface $logger, UserPasswordHasherInterface $hasher)
    {
        $this->logger = $logger;
        $this->hasher = $hasher;
    }

    #[Route('/auth', name: 'auth')]
    public function index(): Response
    {
        return $this->render('auth/index.html.twig', [
            'controller_name' => 'AuthController',
        ]);
    }

    #[Route('/auth/register', name: 'auth_register', methods: ['POST'])]
    public function register(Request $request): Response
    {
        try {
            $request = $this->transformJsonBody($request);

            $user = new User();
            $user->setLogin($request->get('login'));
            $user->setPassword($this->hasher->hashPassword($user, $request->get('password')));


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $data = [
                'status' => Response::HTTP_OK,
                'success' => 'User added successfully'
            ];
            return $this->response($data);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());

            $data = [
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => 'Data not valid'
            ];
            return $this->response($data, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    private function response($data, $status = Response::HTTP_OK, $headers = []): JsonResponse
    {
        return new JsonResponse($data, $status, $headers);
    }

    private function transformJsonBody (Request $request): Request
    {
        $data = json_decode($request->getContent(), true);
        if ($data === null) {
            return $request;
        }
        $request->request->replace($data);
        return $request;
    }
}
