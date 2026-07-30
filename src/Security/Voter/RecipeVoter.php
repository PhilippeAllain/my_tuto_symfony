<?php

namespace App\Security\Voter;

use App\Entity\Recipe;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use App\Entity\User;


final class RecipeVoter extends Voter
{
    public const EDIT = 'RECIPE_EDIT';
    public const VIEW = 'RECIPE_VIEW';
    public const CREATE = 'RECIPE_CREATE';
    public const LIST = 'RECIPE_LIST';
    public const LIST_ALL = 'RECIPE_LIST_ALL';


    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html

        return in_array($attribute, [self::CREATE, self::LIST, self::LIST_ALL]) || (in_array($attribute, [self::EDIT, self::VIEW])
            && $subject instanceof Recipe);
    }


    /**
     * @param Recipe|null $subject
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            $vote?->addReason('The user must be logged in to access this resource.');
            return false;
        }


        switch ($attribute) {
            case self::EDIT:

                return $subject->getUser()->getId() === $user->getId();
                break;
            case self::LIST_ALL:
            case self::VIEW:
            case self::LIST:
            case self::CREATE:

                return TRUE;
                break;
        }

        return false;
    }
}
