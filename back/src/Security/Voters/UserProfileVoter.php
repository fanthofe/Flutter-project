<?php

namespace App\Security\Voters;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserProfileVoter extends Voter
{   
    protected function supports($attribute, $subject)
    {  
        return in_array($attribute, ['EDIT', 'DELETE']) && $subject instanceof User; 
    }

    /**
     * @param string $attribute ('EDIT' or 'DELETE')
     * @param User $subject
     * @param TokenInterface $token JWT token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {   
        $user = $this->getUserFromToken($token);
      
        if (!$user instanceof User) {
            return false;
        }
        if ($attribute === 'EDIT') {
            if($user->getId() === $subject->getId()) {
                return true;
            }
            if ($user->getRoles()[0] === 'ROLE_ADMIN') {
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
