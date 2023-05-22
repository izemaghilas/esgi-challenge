<?php

namespace App\Security\Voter;

use App\Service\AuthorizationChecker;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class ContentVoter extends Voter
{
    public const CREATE = 'CONTENT_CREATE';
    public const EDIT = 'CONTENT_EDIT';
    public const DELETE = 'CONTENT_DELETE';
    public const REVIEW = 'CONTENT_REVIEW';
    public const VIEW_VIDEO = 'CONTENT_VIEW_VIDEO';


    public function __construct(
        private readonly AuthorizationChecker $authorizationChecker,
        private readonly EntityManagerInterface $entityManagerInterface
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::CREATE, self::EDIT, self::DELETE, self::REVIEW, self::VIEW_VIDEO])
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
                if ($this->authorizationChecker->isContributor()) {
                    return true;
                }
                break;
            case self::EDIT:
                if ($this->authorizationChecker->isOwner($user, $subject->getCreatorId())) {
                    return true;
                }
                break;
            case self::DELETE:
                if ($this->authorizationChecker->isAdminOrOwner($user, $subject->getCreatorId())) {
                    return true;
                }
                break;
            case self::REVIEW:
                if ($this->authorizationChecker->isAdminOrReviewer()) {
                    return true;
                }
                break;
            case self::VIEW_VIDEO:
                if ($this->authorizationChecker->isOwner($user, $subject->getCreatorId())) {
                    return true;
                }
                if ($this->authorizationChecker->isAdminOrReviewer() && !$subject->isActive()) {
                    return true;
                }
                if (null === $subject->getPrice() || $subject->getPrice() == 0) {
                    return true;
                }
                # TODO: check if user subscribed to the course (once payment functionality is implemented)
                break;
        }

        return false;
    }
}