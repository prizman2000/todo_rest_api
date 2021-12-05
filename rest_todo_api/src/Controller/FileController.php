<?php

namespace App\Controller;

use App\Entity\File;
use App\Repository\FileRepository;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FileController extends AbstractController
{
    #[Route('/file', name: 'upload_file', methods: ['POST'])]
    public function upload_file(Request $request): Response
    {
        try {
            $destination = $this->getParameter('kernel.project_dir') . '/public/uploads';

            foreach ($request->files as $uploadedFile) {
                $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = Urlizer::urlize($originalFilename) . '-' . uniqid() . '.' . $uploadedFile->guessExtension();
                /*
 * @var UploadedFile $uploadedFile
*/
                $uploadedFile->move($destination, $newFilename);

                $file = new File();
                $file->setName($uploadedFile->getClientOriginalName());
                $file->setUniqName($newFilename);
                $file->setSize($uploadedFile->getSize());
                $file->setCreatedAt(new \DateTime('now'));

                $em = $this->getDoctrine()->getManager();

                $em->persist($file);
                $em->flush();
            }

            $data = [
                'status' => Response::HTTP_OK,
                'success' => 'File added successfully',
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

    #[Route('/file', name: 'get_files', methods: ['GET'])]
    public function get_files(FileRepository $fileRepository): Response
    {
        try {
            $files = $fileRepository->findAll();
            $filesArray = [];

            foreach ($files as $file) {
                $postA = [
                    'id' => $file->getId(),
                    'name' => $file->getName(),
                    'uniq_name' => $file->getUniqName(),
                    'size' => $file->getSize(),
                    'created_at' => $file->getCreatedAt(),
                ];
                array_push($filesArray, $postA);
            }

            return $this->response($filesArray);
        } catch (\Exception $e) {
            $data = [
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => 'Data not valid',
            ];

            return $this->response($data, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    #[Route('/file/{name}', name: 'delete_file', methods: ['DELETE'])]
    public function delete_file(FileRepository $fileRepository, $name): Response
    {
        try {
            $fileToDel = $fileRepository->findOneBy(['uniqName' => $name]);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($fileToDel);
            $entityManager->flush();

            $dir = $this->getParameter('kernel.project_dir') . '/public/uploads';
            $fileDir = scandir($dir);
            foreach ($fileDir as $file) {
                if ($file == $name) {
                    array_map('unlink', glob($dir . '/' . $file));
                }
            }
            $res = [
                'status' => Response::HTTP_OK,
                'success' => 'File deleted successfully',
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

    #[Route('/file/{name}', name: 'download_file', methods: ['GET'])]
    public function download_file($name): Response
    {
        try {
            $file_path = $this->getParameter('kernel.project_dir') . '/public/uploads/' . $name;

            return new BinaryFileResponse($file_path);
        } catch (\Exception $e) {
            $data = [
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                'errors' => 'Data not valid',
            ];

            return $this->response($data, Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    private function response($data, $status = Response::HTTP_OK, $headers = []): JsonResponse
    {
        return new JsonResponse($data, $status, $headers);
    }

    private function transformJsonBody(Request $request): Request
    {
        $data = json_decode($request->getContent(), true);
        if (null === $data) {
            return $request;
        }
        $request->request->replace($data);

        return $request;
    }
}
