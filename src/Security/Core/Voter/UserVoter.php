<?php

namespace App\Security\Core\Voter;

use App\Entity\Core\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{
    private const VIEW = 'view';
    private const EDIT = 'edit';

    public function supportsAttribute(string $attribute): bool
    {
        // Caching the answer if its false
        return in_array($attribute, [self::VIEW, self::EDIT]);
    }

    public function supportsType(string $subjectType): bool
    {
        // Caching the answer if its false
        return $subjectType === User::class;
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
     * @param User $subject
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        return match($attribute) {
            self::VIEW => $this->hasRole(self::VIEW, $user),
            self::EDIT => $this->hasRole(self::EDIT, $user),
            default => throw new \LogicException('This code should not be reached!')
        };
    }

    private function hasRole(string $roleName, User $user): bool
    {
        // TODO: create database call
        return false;
    }
}
