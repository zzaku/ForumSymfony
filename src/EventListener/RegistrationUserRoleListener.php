<?php
namespace App\EventListener;

use App\Entity\User;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class RegistrationUserRoleListener
{

    public function prePersist(User $user, LifecycleEventArgs $event): void
    {
        // Logique pour définir les rôles lors de l'inscription
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