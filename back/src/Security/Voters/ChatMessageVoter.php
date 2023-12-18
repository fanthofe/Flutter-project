<?php

namespace App\Security\Voters;

use App\Entity\ChatMessage;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ChatMessageVoter extends Voter
{   
    protected function supports($attribute, $subject)
    {  
        return in_array($attribute, ['EDIT', 'DELETE']) && $subject instanceof ChatMessage; 
    }

    /**
     * @param string $attribute ('EDIT' or 'DELETE')
     * @param ChatMessage $subject
     * @param TokenInterface $token JWT token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {   

        $user = $this->getUserFromToken($token);
      
        if (!$user instanceof User) {
            return false;
        }

        if ($attribute === 'EDIT' || $attribute === 'DELETE') {
            
            // On request, if $user->getId() === $subject->getAuthor() return true else return false
            if($user->getId() === $subject->getAuthor()->getId()) {
                return true;
            }
            if($user->getRoles()[0] === 'ROLE_ADMIN') {
                // allow admins to edit or delete any chat message
                return true;
            }

        }

        return false;
    }

    /**
     * Get user from token (JWT)
     * 
     * @param TokenInterface $token
     * @return User|null
     */
    private function getUserFromToken(TokenInterface $token)
    {   
        $user = $token->getUser();
        return $user;
    }
}
