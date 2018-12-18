<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Projects;
use App\Entity\User;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
class ProjectsVoter extends Voter
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
        
        if (!$subject instanceof Projects) {
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
        // if they can edit, they can view
        if ($this->canEdit($post, $user)) {
            return true;
        }
        // the Post object could have, for example, a method isPrivate()
        // that checks a boolean $private property
        return !$post->isPrivate();
    }
    private function canEdit(Projects $projects, User $user){
        return $user->getId() === $projects->getUserId();
    }

}
