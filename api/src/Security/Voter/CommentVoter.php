<?php

namespace App\Security\Voter;

use App\Service\AuthorizationUtils;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class CommentVoter extends Voter
{
    public const DELETE = 'COMMENT_DELETE';

    private $authorizationUtils = null;
    
    public function __construct(AuthorizationUtils $authorizationUtils)
    {
        $this->authorizationUtils = $authorizationUtils;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::DELETE])
            && $subject instanceof \App\Entity\Comment;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::DELETE:
                if ($this->authorizationUtils->isAdmin()) { return true; }
                break;
        }

        return false;
    }
}
