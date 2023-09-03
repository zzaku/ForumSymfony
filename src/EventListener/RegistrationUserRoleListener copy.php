<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;



class RegistrationUserRoleListener
{
    private $tokenStorage;
    private $entityManager;

    public function __construct(TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $authorizationChecker, EntityManagerInterface $entityManager)
    {
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
        $this->entityManager = $entityManager;
    }

    public function prePersist(User $user, LifecycleEventArgs $event): void
    {
        $token = $this->tokenStorage->getToken();
        
        if ($token !== null) {
            $currentUser = $token->getUser();
            
            // Vérifiez si l'utilisateur actuel n'est pas l'administrateur connecté
            if ($currentUser instanceof User && !$this->authorizationChecker->isGranted('ROLE_ADMIN')) {
                $email = $user->getEmail();
   
                if (strpos($email, '@insider.fr') !== false) {
                    $user->setRoles(['ROLE_INSIDER']);
                } elseif (strpos($email, '@collaborator.fr') !== false) {
                    $user->setRoles(['ROLE_COLLABORATOR']);
                } elseif (strpos($email, '@external.fr') !== false) {
                    $user->setRoles(['ROLE_EXTERNAL']);
                } else {
                    $user->setRoles(['ROLE_EXTERNAL']);
                }
            }
        }
    }
}
?>