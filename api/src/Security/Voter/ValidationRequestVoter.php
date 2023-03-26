<?php

namespace App\Security\Voter;

use App\Service\AuthorizationChecker;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ValidationRequestVoter extends Voter
{
    public const CREATE = 'VALIDATION_REQUEST_CREATE';

    private $authorizationUtils = null;

    public function __construct(AuthorizationChecker $authorizationUtils)
    {
        $this->authorizationUtils = $authorizationUtils;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::CREATE])
            && $subject instanceof \App\Entity\ValidationRequest;
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
        }

        return false;
    }
}
