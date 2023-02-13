<?php

namespace App\Service;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthorizationUtils
{
    private const ADMIN = 'ROLE_ADMIN';
    private const CONTRIBUTOR = 'ROLE_CONTRIBUTOR';
    private const REVIEWER = 'ROLE_REVIEWER';

    private $security = null;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function isOwner(UserInterface $user, mixed $subject) {
        if ($user == $subject) {
            return true;
        }
        
        return false;
    }

    public function isAdmin() {
        if($this->security->isGranted(self::ADMIN)) {
            return true;
        }
        return false;
    }

    public function isContributor() {
        if($this->security->isGranted(self::CONTRIBUTOR)) {
            return true;
        }
        return false;
    }

    public function isReviewer() {
        if($this->security->isGranted(self::REVIEWER)) {
            return true;
        }
        return false;
    }

    public function isAdminOrOwner(UserInterface $user, mixed $subject) {
        if ($this->isAdmin() || $this->isOwner($user, $subject)) {
            return true;
        }
        return false;
    }
    
    public function isAdminOrReviewer() {
        if ($this->isAdmin() || $this->isReviewer()) {
            return true;
        }
        return false;
    }
}