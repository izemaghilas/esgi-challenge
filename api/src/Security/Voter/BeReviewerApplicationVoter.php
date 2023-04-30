<?php

namespace App\Security\Voter;

use App\Service\AuthorizationChecker;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class BeReviewerApplicationVoter extends Voter
{
    public const CREATE = 'BE_REVIEWER_APPLICATION_CREATE';
    public const VALIDATE = 'BE_REVIEWER_APPLICATION_VALIDATE';
    public const VIEW = 'BE_REVIEWER_APPLICATION_VIEW';

    public function __construct(private readonly AuthorizationChecker $authorizationUtils)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::CREATE, self::VALIDATE, self::VIEW])
            && $subject instanceof \App\Entity\BeReviewerApplication;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::CREATE:
                if ($this->authorizationUtils->isContributor()) {
                    return true;
                }
                break;
            case self::VALIDATE:
                if ($this->authorizationUtils->isAdmin()) {
                    return true;
                }
                break;
            case self::VIEW:
                if ($this->authorizationUtils->isAdminOrOwner($user, $subject->getContributor())) {
                    return true;
                }
                break;
        }

        return false;
    }
}