<?php

/**
 * Authorization Controller.
 *
 * @category Controller
 *
 * @author   Levedev Viacheslav <prizman2000@mail.ru>
 * @license  aaa
 *
 * @see
 */

namespace App\Controller;

use App\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AppController
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
        return $this->render(
            'auth/index.html.twig',
            [
            'controller_name' => 'AuthController',
            ]
        );
    }

    #[Route('/auth/register', name: 'auth_register', methods: ['POST'])]
    public function register(Request $request): Response
    {
        try {
            $request = $this->transformJsonBody($request);

            $user = new User();
            $user->setLogin($request->get('login'));
            $user->setRole('admin');
            $user->setPassword($this->hasher->hashPassword($user, $request->get('password')));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $data = [
                'status' => Response::HTTP_OK,
                'success' => 'User added successfully',
            ];

            return $this->response($data);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());

            $data = [
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => 'Data not valid',
            ];

            return $this->response($data, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    #[Route('/api/role', name: 'auth_role', methods: ['GET'])]
    public function check_role(): Response
    {
        try {
            $user = $this->getUser();

            if($user)
            {
                $role = $user->getRole();

                $data = [
                    'status' => Response::HTTP_OK,
                    'role' => $role,
                    'success' => 'Post added successfully',
                ];

                return $this->response($data);
            } else {
                $data = [
                    'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'errors' => 'Data not valid',
                ];

                return $this->response($data, Response::HTTP_UNPROCESSABLE_ENTITY);
            }
        } catch (\Exception) {
            $error = [
                'status' => Response::HTTP_UNAUTHORIZED,
                'errors' => 'Unauthorized',
            ];

            return $this->response($error, Response::HTTP_UNAUTHORIZED);
        }
    }
}
