<?php

namespace App\EventListener;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;

class LoginSuccessListener
{

    /**
     * Add user information to the login response
     *
     * @param AuthenticationSuccessEvent $event
     * @return void
     */
    public function onLoginSuccess(AuthenticationSuccessEvent $event): void
    {   

        $user = $event->getUser();
        $payload = $event->getData();
        
        if (!$user instanceof User) {
            return;
        }
        
        // Add information to user payload
        $payload['user'] = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'isSubscriber' => $user->isSubscriber(),
            'parent' => $user->isParent(),
        ];

        $event->setData($payload);
    }
}