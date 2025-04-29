<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\AppAuthenticator;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

/**
 * Controller responsible for user registration and email verification.
 */
class RegistrationController extends AbstractController
{
  /**
   * Constructor for RegistrationController.
   * 
   * @param EmailVerifier $emailVerifier The email verification service.
   */
  public function __construct(
    private EmailVerifier $emailVerifier
  ) {}

  /**
   * Registers a new user.
   * 
   * This method creates a new user, validates the registration form, and sends a verification
   * email to the user with a link to confirm their email and activate their account.
   * 
   * @param Request $request The HTTP request.
   * @param UserPasswordHasherInterface $userPasswordHasher The password hashing service.
   * @param Security $security The security service for user authentication.
   * @param EntityManagerInterface $entityManager The entity manager service (Doctrine).
   * 
   * @return Response The HTTP response.
   */
  #[Route('/register', name: 'app_register')]
  public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
  {
    $user = new User();
    $form = $this->createForm(RegistrationFormType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $plainPassword = $form->get('password')->getData();

      // encode the plain password
      $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
      $user->setIsVerified(false);
      $user->setActive(true);
      $user->setRoles(['ROLE_USER']);
      $user->setCreatedAt(new \DateTimeImmutable());
      $user->setUpdatedAt(new \DateTimeImmutable());

      $entityManager->persist($user);
      $entityManager->flush();

      // generate a signed url and email it to the user
      $this->emailVerifier->sendEmailConfirmation(
        'app_verify_email',
        $user,
        (new TemplatedEmail())
          ->from(new Address('tiago.machado.1987@gmail.com', 'Mail'))
          ->to((string) $user->getEmail())
          ->subject('Please Confirm your Email')
          ->htmlTemplate('registration/confirmation_email.html.twig')
      );

      // do anything else you need here, like send an email
      $this->addFlash('info', 'Please verify your email to activate your account.');
      return $security->login($user, AppAuthenticator::class, 'main');

      // return $this->redirectToRoute('app_login');
    }

    return $this->render('registration/register.html.twig', [
      'form' => $form,
    ]);
  }

  /**
   * Verifies the user's email after registration.
   * 
   * This method validates the email confirmation link, sets `User::isVerified=true`, and persists the change.
   * 
   * @param Request $request The HTTP request.
   * @param TranslatorInterface $translator The translator service for error messages.
   * 
   * @return Response The HTTP response.
   */
  #[Route('/verify/email', name: 'app_verify_email')]
  public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
  {
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

    // validate email confirmation link, sets User::isVerified=true and persists
    try {
      /** @var User $user */
      $user = $this->getUser();
      $this->emailVerifier->handleEmailConfirmation($request, $user);
    } catch (VerifyEmailExceptionInterface $exception) {
      $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

      return $this->redirectToRoute('app_register');
    }

    $this->addFlash('success', 'Your email has been successfully verified.');

    return $this->redirectToRoute('app_home');
  }
}
