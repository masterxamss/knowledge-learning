<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * Custom constraint to check if two passwords match.
 * 
 * This constraint is used to validate that two password fields in a form are identical.
 * It can be applied to form fields where the password and confirmation password should be the same.
 * 
 * @Annotation
 */
class PasswordMatch extends Constraint
{
  /**
   * Error message to be displayed when the passwords do not match.
   * 
   * @var string
   */
  public $message = 'Les mots de passe ne correspondent pas.';
}
