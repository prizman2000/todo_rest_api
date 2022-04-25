<?php

namespace App\Controller;

use App\Entity\Post;
use App\Repository\PostRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AppController
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route('/api/post', name: 'post_add', methods: ['POST'])]
    public function add_post(Request $request): Response
    {
        try {
            $user = $this->getUser();

            try {
                $request = $this->transformJsonBody($request);

                $post = new Post();
                $post->setTitle($request->get('title'));
                $post->setDescription($request->get('description'));

                $post->setUser($user);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($post);
                $entityManager->flush();

                $data = [
                    'status' => Response::HTTP_OK,
                    'success' => 'Post added successfully',
                ];

                return $this->response($data);
            } catch (\Exception $e) {
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

    #[Route('/api/post/{id}', name: 'post_update', methods: ['PUT'])]
    public function update_post(Request $request, PostRepository $postRepository, $id): Response
    {
        try {
            $user = $this->getUser();
            $request = $this->transformJsonBody($request);

            $post = $postRepository->find($id);
            if ($post->getUser()->getId() !== $user->getId()) {
                $data = [
                    'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'errors' => 'Data not valid',
                ];

                return $this->response($data, Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            $post->setTitle($request->get('title'));
            $post->setDescription($request->get('description'));

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($post);
            $entityManager->flush();

            $data = [
                'status' => Response::HTTP_OK,
                'success' => 'Post added successfully',
            ];

            return $this->response($data);
        } catch (\Exception $e) {
            $data = [
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => 'Data not valid',
            ];

            return $this->response($data, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    #[Route('/api/post', name: 'post_get_all', methods: ['GET'])]
    public function get_posts(PostRepository $postRepository): Response
    {
        try {
            $user = $this->getUser();
            $posts = $postRepository->findAll();

            $postArray = [];

            foreach ($posts as $post) {
                if ($post->getUser()->getId() === $user->getId()) {
                    $postA = [
                        'id' => $post->getId(),
                        'title' => $post->getTitle(),
                        'description' => $post->getDescription(),
                    ];
                    array_push($postArray, $postA);
                }
            }

            return $this->response($postArray);
        } catch (\Exception $e) {
            $data = [
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => 'Data not valid',
            ];

            return $this->response($data, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    #[Route('/api/post/{id}', name: 'post_get', methods: ['GET'])]
    public function get_post(PostRepository $postRepository, $id): Response
    {
        try {
            $user = $this->getUser();
            $post = $postRepository->find($id);

            if ($post->getUser()->getId() !== $user->getId()) {
                $data = [
                    'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'errors' => 'Data not valid',
                ];

                return $this->response($data, Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $postA = [
                'title' => $post->getTitle(),
                'description' => $post->getDescription(),
            ];

            return $this->response($postA);
        } catch (\Exception $e) {
            $data = [
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => 'Data not valid',
            ];

            return $this->response($data, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    #[Route('/api/post/{id}', name: 'post_delete', methods: ['DELETE'])]
    public function delete_post(PostRepository $postRepository, $id): Response
    {
        try {
            $user = $this->getUser();
            $post = $postRepository->find($id);

            if ($post->getUser()->getId() !== $user->getId()) {
                $data = [
                    'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'errors' => 'Data not valid',
                ];

                return $this->response($data, Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($post);
            $entityManager->flush();
            $res = [
                'status' => Response::HTTP_OK,
                'success' => 'Post deleted successfully',
            ];

            return $this->response($res);
        } catch (\Exception $e) {
            $data = [
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => 'Data not valid',
            ];

            return $this->response($data, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
