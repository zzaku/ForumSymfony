<?php

namespace App\Controller;

use App\Form\UserProfileType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProfileController extends AbstractController
{
    #[Route("/profile", name: "profile")]
    public function show(Request $request)
    {
        // Récupérez l'utilisateur actuellement connecté
        $user = $this->getUser();

        // Créez le formulaire de profil avec les données de l'utilisateur
        $form = $this->createForm(UserProfileType::class, $user);

        // Affichez la vue du formulaire
        return $this->render('profile/show.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route("/profile/update", name: "update_profile")]
    public function update(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher)
    {
        $user = $this->getUser();
    
        // Créez le formulaire de profil avec les données de l'utilisateur
        $form = $this->createForm(UserProfileType::class, $user);
    
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $userData = $form->getData();
    
            if ($userData->getPlainPassword()) {
                $hashedPassword = $passwordHasher->hashPassword($user, $userData->getPlainPassword());
                $user->setPassword($hashedPassword);
            }
    
            $entityManager->persist($user);
            $entityManager->flush();
    
            $this->addFlash('success', 'Profil mis à jour avec succès.');
    
            return $this->redirectToRoute('home');
        }
    
        return $this->render('profile/show.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}