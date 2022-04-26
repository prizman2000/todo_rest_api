<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\GroupPost;
use App\Entity\GroupUser;
use App\Repository\GroupPostRepository;
use App\Repository\GroupRepository;
use App\Repository\GroupUserRepository;
use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GroupController extends AppController
{
    #[Route('/api/group', name: 'group_get_all', methods: ['GET'])]
    public function get_groups(GroupRepository $groupRepository): Response
    {
        try
        {
            $user = $this->getUser();
            $groups = $groupRepository->findAll();
            $groupArray = [];

            foreach ($groups as $group)
            {
                if ($group->getOwnerId() === $user->getId())
                {

                    $owner_group = [
                        'id' => $group->getId(),
                        'owner' => true,
                        'name' => $group->getName()
                    ];
                } else {
                    $owner_group = [
                        'id' => $group->getId(),
                        'owner' => false,
                        'name' => $group->getName()
                    ];
                }
                array_push($groupArray, $owner_group);
            }
            return $this->response($groupArray);
        } catch (\Exception $e) {
            $data = [
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => $e->getMessage(),
            ];
            return $this->response($data, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    #[Route('/api/group', name: 'group_add', methods: ['POST'])]
    public function add_group(GroupRepository $groupRepository, Request $request): Response
    {
        try {
            $user = $this->getUser();

            try {
                $request = $this->transformJsonBody($request);

                if ($groupRepository->findOneBy(["owner_id" => $user->getId(), "name" => $request->get('name')]))
                {
                    $data = [
                        'status' => Response::HTTP_OK,
                        'exist' => true,
                        'success' => 'Group already exist',
                    ];

                } else {
                    $group = new Group();
                    $group->setOwnerId($user->getId());
                    $group->setName($request->get('name'));

                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($group);
                    $entityManager->flush();

                    $data = [
                        'status' => Response::HTTP_OK,
                        'success' => 'Group added successfully'
                    ];

                }
                return $this->response($data);
            } catch (\Exception $e) {
                $data = [
                    'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'errors' => $e->getMessage(),
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

    #[Route('/api/group/{id}', name: 'group_del', methods: ['DELETE'])]
    public function delete_group(GroupRepository $groupRepository, $id): Response
    {
        try {
            $user = $this->getUser();

            try {
                $group = $groupRepository->find($id);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($group);
                $entityManager->flush();

                $data = [
                    'status' => Response::HTTP_OK,
                    'success' => 'Group deleted successfully'
                ];

                return $this->response($data);
            } catch (\Exception $e) {
                $data = [
                    'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'errors' => $e->getMessage(),
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

    #[Route('/api/group-post/{id}', name: 'group_get_post', methods: ['GET'])]
    public function get_posts_in_group(GroupPostRepository $groupPostRepository, PostRepository $postRepository, $id): Response
    {
        try {
            $user = $this->getUser();

            try {
                $groups = $groupPostRepository->findAll();
                $groups_res = [];

                foreach ($groups as $item)
                {
                    if ($item->getId() == $id)
                    {
                        $post = $postRepository->find($item->getPostId());
                        array_push($groups_res, ["title" => $post->getTitle(), "description" => $post->getDescription()]);
                    }
                }

                return $this->response($groups_res);
            } catch (\Exception $e) {
                $data = [
                    'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'errors' => $e->getMessage(),
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

    #[Route('/api/group-add-post', name: 'group_add_post', methods: ['POST'])]
    public function add_post_in_group(Request $request): Response
    {
        try {
            $user = $this->getUser();

            try {
                $request = $this->transformJsonBody($request);

                $group_post = new GroupPost();
                $group_post->setGroupId($request->get('group_id'));
                $group_post->setPostId($request->get('post_id'));

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($group_post);
                $entityManager->flush();

                $data = [
                    'status' => Response::HTTP_OK,
                    'success' => 'Post added in group successfully'
                ];

                return $this->response($data);
            } catch (\Exception $e) {
                $data = [
                    'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'errors' => $e->getMessage(),
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

    #[Route('/api/group-delete-post/{id}', name: 'group_delete_post', methods: ['DELETE'])]
    public function delete_post_in_group(GroupPostRepository $groupPostRepository, $id): Response
    {
        try {
            $user = $this->getUser();

            try {
                $group = $groupPostRepository->find($id);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($group);
                $entityManager->flush();

                $data = [
                    'status' => Response::HTTP_OK,
                    'success' => 'Post deleted in group successfully'
                ];

                return $this->response($data);
            } catch (\Exception $e) {
                $data = [
                    'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'errors' => $e->getMessage(),
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

    #[Route('/api/subscribe', name: 'subscribe', methods: ['POST'])]
    public function subscribe(Request $request): Response
    {
        try {
            $user = $this->getUser();

            try {
                $request = $this->transformJsonBody($request);

                $group_user = new GroupUser();
                $group_user->setGroupId($request->get('group_id'));
                $group_user->setUserId($user->getId());

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($group_user);
                $entityManager->flush();

                $data = [
                    'status' => Response::HTTP_OK,
                    'success' => 'Sub make successfully'
                ];

                return $this->response($data);
            } catch (\Exception $e) {
                $data = [
                    'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'errors' => $e->getMessage(),
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

    #[Route('/api/get-subscriptions', name: 'get_subscriptions', methods: ['GET'])]
    public function get_subscriptions(GroupUserRepository $groupUserRepository, GroupRepository $groupRepository): Response
    {
        try {
            $user = $this->getUser();
            $data = [];

            try {
                $groupUser = $groupUserRepository->findBy(['user_id' => $user->getId()]);

                foreach ($groupUser as $item)
                {
                    $group = $groupRepository->find($item->getGroupId());
                    array_push($data, ['id' => $group->getId(), 'name' => $group->getName()]);
                }

                return $this->response($data);
            } catch (\Exception $e) {
                $data = [
                    'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'errors' => $e->getMessage(),
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
