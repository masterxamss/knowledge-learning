<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Controller responsible for user authentication (login and logout).
 */
class SecurityController extends AbstractController
{
  /**
   * Displays the login page and handles authentication.
   * 
   * This method renders the login form and checks for any authentication errors.
   * It also retrieves the last username entered by the user.
   * 
   * @param AuthenticationUtils $authenticationUtils The authentication utility service to handle authentication-related logic.
   * 
   * @return Response The HTTP response containing the rendered login page.
   */
  #[Route(path: '/login', name: 'app_login', methods: ['GET', 'POST'])]
  public function login(AuthenticationUtils $authenticationUtils): Response
  {
    // If the user is already authenticated, redirect to the home page
    if ($this->getUser()) {
      return $this->redirectToRoute('app_home');
    }

    // Get the login error if there is one
    $error = $authenticationUtils->getLastAuthenticationError();
    // Get the last username entered by the user
    $lastUsername = $authenticationUtils->getLastUsername();

    // Render the login page with the error and the last username entered
    return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
  }

  /**
   * Handles the user logout process.
   * 
   * This method will never be executed directly as it is intercepted by the firewall's logout mechanism.
   * 
   * @throws \LogicException This exception is thrown because the method is intercepted by the logout key on the firewall.
   */
  #[Route(path: '/logout', name: 'app_logout')]
  public function logout(): void
  {
    throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
  }
}
