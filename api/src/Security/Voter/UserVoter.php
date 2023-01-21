<?php

namespace App\Security\Voter;

use App\Service\AuthorizationUtils;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{
    public const CREATE = 'USER_CREATE';
    public const EDIT = 'USER_EDIT';
    public const VIEW = 'USER_VIEW';
    public const DELETE = 'USER_DELETE';

    private $authorizationUtils = null;

    public function __construct(AuthorizationUtils $authorizationUtils)
    {
        $this->authorizationUtils = $authorizationUtils;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::CREATE, self::EDIT, self::VIEW, self::DELETE])
            && $subject instanceof \App\Entity\User;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::CREATE:
                if ($this->authorizationUtils->isAdmin()) { return true; }
                break;
            case self::EDIT:
                if ($this->authorizationUtils->isAdminOrOwner($user, $subject)) { return true; }
                break;
            case self::VIEW:
                if ($this->authorizationUtils->isAdminOrOwner($user, $subject)) { return true; }
                break;
            case self::DELETE:
                if ($this->authorizationUtils->isAdminOrOwner($user, $subject)) { return true; }
                break;
        }

        return false;
    }
}
