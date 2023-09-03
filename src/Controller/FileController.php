<?php

namespace App\Controller;

use App\Entity\File;
use App\Repository\FileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FileController extends AbstractController
{
    #[Route("/file/{id}", name: "file_download")]
    public function download(int $id, FileRepository $fileRepository): Response
    {
        $file = $fileRepository->find($id);

        if (!$file) {
            throw $this->createNotFoundException('The file does not exist');
        }

        $fileContent = $file->getFileContent();

        $response = new Response($fileContent);
        $response->headers->set('Content-Type', 'application/octet-stream');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $file->getFilename() . '"');

        return $response;
    }
}
