<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;


/**
 * Service responsible for sending email confirmations and handling email verification.
 * 
 * This service sends email confirmation links and verifies email addresses for users.
 */
class EmailVerifier
{
  /**
   * Constructor to initialize the dependencies required for email verification.
   * 
   * @param VerifyEmailHelperInterface $verifyEmailHelper The service used for generating and validating email verification URLs.
   * @param MailerInterface $mailer The mailer service for sending emails.
   * @param EntityManagerInterface $entityManager The Doctrine Entity Manager for persisting and flushing user data.
   */
  public function __construct(
    private VerifyEmailHelperInterface $verifyEmailHelper,
    private MailerInterface $mailer,
    private EntityManagerInterface $entityManager
  ) {}

  /**
   * Sends a confirmation email to the user with a signed URL for email verification.
   * 
   * This method generates a signed URL for email verification, attaches it to the email context,
   * and sends the email to the user.
   * 
   * @param string $verifyEmailRouteName The route name used to generate the verification URL.
   * @param User $user The user to whom the email is being sent.
   * @param TemplatedEmail $email The email object containing the template and context for the email.
   */
  public function sendEmailConfirmation(string $verifyEmailRouteName, User $user, TemplatedEmail $email): void
  {
    $signatureComponents = $this->verifyEmailHelper->generateSignature(
      $verifyEmailRouteName,
      (string) $user->getId(),
      (string) $user->getEmail()
    );

    $context = $email->getContext();
    $context['signedUrl'] = $signatureComponents->getSignedUrl();
    $context['expiresAtMessageKey'] = $signatureComponents->getExpirationMessageKey();
    $context['expiresAtMessageData'] = $signatureComponents->getExpirationMessageData();

    $email->context($context);

    $this->mailer->send($email);
  }

  /**
   * Handles the email confirmation process by validating the confirmation request.
   * 
   * This method validates the email confirmation from the request, updates the user's verification status, 
   * and persists the changes to the database.
   * 
   * @param Request $request The HTTP request containing the email confirmation data.
   * @param User $user The user whose email is being verified.
   * 
   * @throws VerifyEmailExceptionInterface If the email confirmation validation fails.
   */    public function handleEmailConfirmation(Request $request, User $user): void
  {
    $this->verifyEmailHelper->validateEmailConfirmationFromRequest($request, (string) $user->getId(), (string) $user->getEmail());

    $user->setIsVerified(true);

    $this->entityManager->persist($user);
    $this->entityManager->flush();
  }
}
