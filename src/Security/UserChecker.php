<?php

namespace App\Security;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
  public function checkPreAuth(UserInterface $user): void
  {
    if (!$user instanceof \App\Entity\User) {
      return;
    }

    if (!$user->getIsVerified()) {
      throw new CustomUserMessageAuthenticationException('Votre compte n\'a pas encore été vérifié. Vérifiez votre e-mail.');
    }


    if (!$user->getActive()) {
      throw new CustomUserMessageAuthenticationException('Votre compte a été suspendu. Contactez un administrateur.');
    }
  }

  public function checkPostAuth(UserInterface $user): void {}
}
