<?php

namespace App\Controller;

use App\Entity\File;
use App\Entity\Message;
use App\Entity\Topic;
use App\Form\MessageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MessageController extends AbstractController
{
    #[Route("/board/{board_id}/topic/{topic_id}", name: "topic_detail")]
    public function detail(Request $request, EntityManagerInterface $em, int $board_id, int $topic_id)
    {
        $topic = $em->getRepository(Topic::class)->find($topic_id);

        if (!$topic) {
            throw $this->createNotFoundException('The topic does not exist');
        }

        $message = new Message();
        $message->setTopic($topic);
        $form = $this->createForm(MessageType::class, $message);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile[] $files */
            $files = $form->get('files')->getData();

            foreach ($files as $uploadedFile) {
                $fileContent = file_get_contents($uploadedFile->getPathname());

                $fileEntity = new File();
                $fileEntity->setFilename($uploadedFile->getClientOriginalName());
                $fileEntity->setFileContent($fileContent);
                $fileEntity->setMessage($message);

                $em->persist($fileEntity);
            }

            $em->persist($message);
            $em->flush();

            return $this->redirectToRoute('topic_detail', ['board_id' => $board_id, 'topic_id' => $topic_id]);
        }

        return $this->render('topic/detail.html.twig', [
            'topic' => $topic,
            'form' => $form->createView(),
        ]);
    }
}
