<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\EmailDomainFormType;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;

class UserController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/admin/user/list', name: 'admin_user_list')]
    #[IsGranted('ROLE_ADMIN')]
    public function userList(): Response
    {
        $users = $this->entityManager->getRepository(User::class)->findAll();

        return $this->render('manage/user_list.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/admin/user/profile/{id}', name: 'user_profile')]
    #[IsGranted('ROLE_ADMIN')]
    public function show( int $id, Request $request): Response
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur introuvable');
        }

        $email = $user->getEmail();
        $emailParts = explode('@', $email);
        $emailDomain = end($emailParts);

        $form = $this->createForm(EmailDomainFormType::class, ['emailDomain' => $emailDomain]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->redirectToRoute('admin_user_profile_update', ['id' => $id]);
        }
        
        return $this->render('manage/user_profile.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/admin/user/profile/{id}/update', name: 'admin_user_profile_update')]
    #[IsGranted('ROLE_ADMIN')]
    public function updateProfile(Request $request, int $id): Response
        {
            $user = $this->entityManager->getRepository(User::class)->find($id);

            if (!$user) {
                throw $this->createNotFoundException('Utilisateur introuvable');
            }

            $form = $this->createForm(EmailDomainFormType::class);
            $form->handleRequest($request);
        
            if ($form->isSubmitted() && $form->isValid()) {
                $newDomain = $form->get('emailDomain')->getData();
                $email = $user->getEmail();
                $emailParts = explode('@', $email);
                $newEmail = $emailParts[0] . '@' . $newDomain;
                $user->setEmail($newEmail);
                
                $entityManager = $this->entityManager;
                $entityManager->persist($user);
                $entityManager->flush();
                
                $this->addFlash('success', 'Profil mis à jour avec succès.');
            }
            return $this->redirectToRoute('user_profile', ['id' => $id]);
        }

    #[Route(path: '/admin/user/block/{id}', name: 'admin_user_block')]
    public function blockUser(User $user, int $id): Response
        {

            $user = $this->entityManager->getRepository(User::class)->find($id);

            if (!$user) {
                throw $this->createNotFoundException('Utilisateur introuvable');
            }
            
            $user->setIsBlocked(true);

            $entityManager = $this->entityManager;
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur bloqué avec succès.');

            return $this->redirectToRoute('admin_user_list');
        }

    #[Route(path: '/admin/user/unblock/{id}', name: 'admin_user_unblock')]
    public function unblockUser(User $user, int $id): Response
        {

            $user = $this->entityManager->getRepository(User::class)->find($id);

            if (!$user) {
                throw $this->createNotFoundException('Utilisateur introuvable');
            }
            
            $user->setIsBlocked(false);

            $entityManager = $this->entityManager;
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur bloqué avec succès.');

            return $this->redirectToRoute('admin_user_list');
        }
}