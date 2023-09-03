<?php

namespace App\Controller;

use App\Entity\Board;
use App\Entity\Message;
use App\Entity\Topic;
use App\Form\MessageType;
use App\Form\TopicType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TopicController extends AbstractController
{
    #[Route("/board/{board_id}/topics", name: "topic_list")]
    public function list(EntityManagerInterface $em, int $board_id)
    {
        $topics = $em->getRepository(Topic::class)->findBy(['board' => $board_id]);

        return $this->render('topic/list.html.twig', [
            'topics' => $topics,
            'board_id' => $board_id,
        ]);
    }

    #[Route("/board/{board_id}/topic/create", name: "topic_create")]
    public function create(Request $request, EntityManagerInterface $em, int $board_id)
    {
        $board = $em->getRepository(Board::class)->find($board_id);

        if (!$board) {
            throw $this->createNotFoundException('The board does not exist');
        }

        $topic = new Topic();
        $topic->setBoard($board);
        $topic->setCreatedAt(new \DateTime());

        $form = $this->createForm(TopicType::class, $topic);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($topic);
            $em->flush();

            return $this->redirectToRoute('topic_list', ['board_id' => $board_id]);
        }

        return $this->render('topic/create.html.twig', [
            'form' => $form->createView(),
            'board_id' => $board_id,
        ]);
    }

    #[Route("/board/{board_id}/topic/{topic_id}", name: "topic_detail", requirements: ["topic_id" => "\d+"])]
    public function detail(EntityManagerInterface $em, Request $request, int $board_id, int $topic_id)
    {
        $topic = $em->getRepository(Topic::class)->find($topic_id);
        $messages = $em->getRepository(Message::class)->findBy(['topic' => $topic]);

        $message = new Message();
        $message->setTopic($topic);

        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($message);
            $em->flush();

            return $this->redirectToRoute('topic_detail', [
                'board_id' => $board_id,
                'topic_id' => $topic_id,
            ]);
        }

        return $this->render('topic/detail.html.twig', [
            'topic' => $topic,
            'messages' => $messages,
            'form' => $form->createView(),
        ]);
    }
}
