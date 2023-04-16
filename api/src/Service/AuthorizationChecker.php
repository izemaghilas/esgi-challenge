<?php

namespace App\Service;

use App\Enums\Role;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthorizationChecker
{
    private $security = null;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function isOwner(UserInterface $user, mixed $owner)
    {
        if ($user === $owner) {
            return true;
        }

        return false;
    }

    public function isAdmin()
    {
        if ($this->security->isGranted(Role::ADMIN->value)) {
            return true;
        }
        return false;
    }

    public function isContributor()
    {
        if ($this->security->isGranted(Role::CONTRIBUTOR->value)) {
            return true;
        }
        return false;
    }

    public function isReviewer()
    {
        if ($this->security->isGranted(Role::REVIEWER->value)) {
            return true;
        }
        return false;
    }

    public function isAdminOrOwner(UserInterface $user, mixed $owner)
    {
        if ($this->isAdmin() || $this->isOwner($user, $owner)) {
            return true;
        }
        return false;
    }

    public function isAdminOrReviewer()
    {
        if ($this->isAdmin() || $this->isReviewer()) {
            return true;
        }
        return false;
    }
}