<?php
namespace App\Security;

use App\Entity\Category;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class CategoryVoter extends Voter
{
    const VIEW = 'view';

    protected function supports(string $attribute, $subject): bool
    {
        // Supporte uniquement l'attribut "VIEW" et l'objet Category
        return $attribute === self::VIEW && $subject instanceof Category;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token, ...$extraArguments): bool
    {
    /** @var Category $category */
    $category = $subject;

    $isAdmin = $extraArguments[0] ?? false;
    $isAuthor = $extraArguments[1] ?? false;
    $hasSameRole = $extraArguments[2] ?? false;

    if ($isAdmin || $isAuthor || $hasSameRole) {
        return true;
    }

    return false; 
    }
}
?>