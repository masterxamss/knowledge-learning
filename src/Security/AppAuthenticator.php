<?php

namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

/**
 * Custom authenticator class for handling the user login process.
 * 
 * This authenticator is responsible for authenticating the user based on the provided credentials (email and password),
 * handling CSRF token validation, and remembering the user if the "Remember Me" option is enabled.
 */
class AppAuthenticator extends AbstractLoginFormAuthenticator
{
  use TargetPathTrait;

  public const LOGIN_ROUTE = 'app_login';

  /**
   * Constructor to initialize the UrlGeneratorInterface service.
   * 
   * @param UrlGeneratorInterface $urlGenerator The URL generator service to generate redirection URLs.
   */
  public function __construct(private UrlGeneratorInterface $urlGenerator) {}

  /**
   * {@inheritDoc}
   *
   * This method is responsible for creating the Passport object, which holds the user credentials and badges for authentication.
   * The CSRF token and "Remember Me" badge are also added to the passport for further security.
   * 
   * @param Request $request The HTTP request object containing the user's credentials (email, password, CSRF token).
   * 
   * @return Passport The passport containing user credentials and security badges.
   */
  public function authenticate(Request $request): Passport
  {
    $email = $request->getPayload()->getString('email');

    // Store the last username in the session
    $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);

    // Return a new passport with the user badge, password credentials, and security badges
    return new Passport(
      new UserBadge($email),
      new PasswordCredentials($request->getPayload()->getString('password')),
      [
        new CsrfTokenBadge('authenticate', $request->getPayload()->getString('_csrf_token')),
        new RememberMeBadge(),
      ]
    );
  }

  /**
   * {@inheritDoc}
   *
   * This method is called when the authentication is successful. It redirects the user to the target page if available,
   * or to the home page by default.
   * 
   * @param Request $request The HTTP request object.
   * @param TokenInterface $token The authentication token.
   * @param string $firewallName The name of the firewall (security context).
   * 
   * @return Response A redirect response to the target URL or default home page.
   */
  public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
  {
    // If there is a target path (previous page the user tried to access), redirect there
    if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
      return new RedirectResponse($targetPath);
    }

    // Otherwise, redirect to the home page
    return new RedirectResponse($this->urlGenerator->generate('app_home'));
  }

  /**
   * {@inheritDoc}
   *
   * This method is called when the user is not authenticated, and it returns the URL of the login page.
   * 
   * @param Request $request The HTTP request object.
   * 
   * @return string The URL of the login page.
   */
  protected function getLoginUrl(Request $request): string
  {
    return $this->urlGenerator->generate(self::LOGIN_ROUTE);
  }
}
