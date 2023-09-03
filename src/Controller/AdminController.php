<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

use App\Entity\User;

class AdminController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    #[Route(path: '/admin/assign-role', name: 'admin_assign_role')]
    public function assignRole(TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $authorizationChecker)
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['username' => 'zakuu']);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur introuvable');
        }

        // Ajoutez le rôle "ROLE_ADMIN"
        $user->addRole('ROLE_ADMIN');

        // Enregistrez les modifications
        $entityManager = $this->entityManager;
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new Response('Rôle attribué avec succès');
    }
}
