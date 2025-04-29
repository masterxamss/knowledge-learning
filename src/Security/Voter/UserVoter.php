<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Voter to authorize access to user-related actions such as viewing and editing a user's profile.
 * 
 * This voter is responsible for determining whether the current user has permission to edit or view another user’s data.
 * It supports two actions:
 * - 'USER_EDIT' to check if a user can edit their own profile.
 * - 'USER_VIEW' to check if a user can view another user's profile.
 */
final class UserVoter extends Voter
{
  public const EDIT = 'USER_EDIT';
  public const VIEW = 'USER_VIEW';

  /**
   * {@inheritDoc}
   *
   * This method checks if the attribute is either 'USER_EDIT' or 'USER_VIEW' and if the subject is a User entity.
   * 
   * @param string $attribute The attribute being checked (either 'USER_EDIT' or 'USER_VIEW').
   * @param mixed $subject The subject being evaluated, expected to be a User entity.
   * 
   * @return bool Returns true if the attribute is supported and the subject is a User entity.
   */
  protected function supports(string $attribute, mixed $subject): bool
  {
    return in_array($attribute, [self::EDIT, self::VIEW])
      && $subject instanceof \App\Entity\User;
  }

  /**
   * {@inheritDoc}
   *
   * This method checks whether the user is authorized to perform the specified action on the given user.
   * 
   * For the 'USER_EDIT' action, the user is allowed to edit their own profile.
   * For the 'USER_VIEW' action, logic should be implemented to determine if the user can view the profile.
   * 
   * @param string $attribute The attribute being checked (either 'USER_EDIT' or 'USER_VIEW').
   * @param mixed $subject The subject being evaluated, expected to be a User entity.
   * @param TokenInterface $token The authentication token containing the current user's details.
   * 
   * @return bool Returns true if the user is authorized to perform the action, false otherwise.
   */
  protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
  {
    $user = $token->getUser();

    // If the user is anonymous, deny access
    if (!$user instanceof User) {
      return false;
    }

    // Logic for 'USER_EDIT' and 'USER_VIEW' actions
    switch ($attribute) {
      case self::EDIT:
        // Users can only edit their own profile
        return $subject->getId() === $user->getId();

      case self::VIEW:
        // Logic to determine if the user can view the profile
        // Implement specific logic for viewing, for now, it returns true
        return true; // Can be customized based on your requirements
    }

    return false;
  }
}
