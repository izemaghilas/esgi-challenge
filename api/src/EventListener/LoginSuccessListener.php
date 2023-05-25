<?php

namespace App\EventListener;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

class LoginSuccessListener
{
    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        $user = $event->getUser();

        if (!$user instanceof User) {
            return;
        }

        if (!$user->isActive()) {
            $data = [
                'user' => [
                    'isActive' => false
                ]
            ];
        } else {
            // Add information to user payload
            $data['user'] = [
                'id' => $user->getId(),
                'firstname' => $user->getFirstname(),
                'lastname' => $user->getLastname(),
                'email' => $user->getEmail(),
                'roles' => $user->getRoles(),
            ];
        }

        $event->setData($data);
    }
}