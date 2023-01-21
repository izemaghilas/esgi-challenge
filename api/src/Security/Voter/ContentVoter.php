<?php

namespace App\Security\Voter;

use App\Service\AuthorizationUtils;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ContentVoter extends Voter
{
    public const CREATE = 'CONTENT_CREATE';
    public const EDIT = 'CONTENT_EDIT';
    public const DELETE = 'CONTENT_DELETE';

    private $authorizationUtils = null;

    public function __construct(AuthorizationUtils $authorizationUtils)
    {
        $this->authorizationUtils = $authorizationUtils;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::CREATE, self::EDIT, self::DELETE])
            && $subject instanceof \App\Entity\Content;
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
                if ($this->authorizationUtils->isContributor()) { return true; }
                break;
            case self::EDIT:
                if ($this->authorizationUtils->isOwner($user, $subject)) { return true; }
                break;
            case self::DELETE:
                if ($this->authorizationUtils->isAdminOrOwner($user, $subject)) { return true; }
                break;
        }

        return false;
    }
}
