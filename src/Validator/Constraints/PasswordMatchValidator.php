<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validator for the PasswordMatch constraint.
 * 
 * This class checks if the 'confirmPassword' field value matches the 'password' field value
 * during form validation. It is typically used for password confirmation validation.
 */
class PasswordMatchValidator extends ConstraintValidator
{
  /**
   * Validates the password confirmation field.
   * 
   * @param mixed $value The value of the 'confirmPassword' field to validate.
   * @param Constraint $constraint The constraint for which the validation is being performed.
   */
  public function validate($value, Constraint $constraint): void
  {
    // Get the form data to access the password field
    $formData = $this->context->getRoot()->getData();

    // Get the password value
    $password = $formData->getPassword();

    // Checks that the value of the 'confirmPassword' field is equal to the value of the 'password' field
    if ($password !== $value) {
      // Adds an error if the passwords don't match
      $this->context->buildViolation($constraint->message)
        ->addViolation();
    }
  }
}
