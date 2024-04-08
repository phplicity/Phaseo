<?php

namespace App\Security\Core\Voter;

use App\Entity\Core\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AccessControlVoter extends Voter
{
    private const USER = 'ROLE_USER';

    public function supportsAttribute(string $attribute): bool
    {
        // Caching the answer if its false
        return $attribute === self::USER;
    }

    public function supportsType(string $subjectType): bool
    {
        // Caching the answer if its false
        return $subjectType === Request::class;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!$this->supportsAttribute($attribute)) {
            return false;
        }

        return $subject instanceof User;
    }

    /**
     * @param string $attribute
     * @param TokenInterface $token
     * @param Request $subject
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        // If the user is logged in, granted for every page
        return $token->getUser() instanceof  User;
    }
}
