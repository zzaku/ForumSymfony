<?php
namespace App\EventListener;

use App\Entity\User;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;



class UpdateUserRoleListener
{
    private $tokenStorage;
    private $entityManager;

    public function __construct(TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $authorizationChecker, EntityManagerInterface $entityManager)
    {
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
        $this->entityManager = $entityManager;
    }

    public function preUpdate(User $user, LifecycleEventArgs $event): void
    {
        $user = $event->getObject();
        if ($user instanceof User) {
            // Vérifiez si l'utilisateur actuel est administrateur
            $token = $this->tokenStorage->getToken();
            
            if ($token !== null) {
                $currentUser = $token->getUser();
                
                if ($currentUser instanceof User && $this->authorizationChecker->isGranted('ROLE_ADMIN')) {
                    $this->preUpdateUserRole($user);
                }
            }
        }
    }

    private function preUpdateUserRole(User $user): void
    {
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
?>