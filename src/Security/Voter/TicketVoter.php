<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\Tickets;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class TicketVoter extends Voter
{
    const EDIT = 'EDIT';
    private $decisionManager;
    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, array(self::EDIT))) {
            return false;
        }
      
        if (!$subject instanceof Tickets) {
            return false;
        }
        return true;

    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        if ($this->decisionManager->decide($token, array('ROLE_ADMIN'))){
            return true;
        }
        if (!$user instanceof User) {
            return false;
        }

        $post = $subject;

        switch ($attribute) {
            case 'EDIT':
                return $this->canEdit($post, $user);
        }

        return false;
        
        throw new \LogicException('This code should not be reached!');
        if ($this->decisionManager->decide($token, array('ROLE_ADMIN'))) {
            return true;
        }
    }
     
    private function canView(Post $post, User $user)
    {
        if ($this->canEdit($post, $user)) {
            return true;
        }
        return !$post->isPrivate();
    }
    private function canEdit(Tickets $tickets, User $user){
        return $user->getId() === $tickets->getUserId();
    }

   
}
