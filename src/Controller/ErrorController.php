<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Controller responsible for handling error pages in the application.
 *
 * This controller manages the display of error pages, specifically for the "404 Not Found" error.
 * It ensures that when users access a non-existent route, they are shown a custom 404 error page.
 * The error handling is simple and is focused on rendering the error page with an appropriate response status.
 */
class ErrorController extends AbstractController
{
  /**
   * Route to handle the display of the 404 error page.
   * 
   * This method is triggered when a 404 error occurs (i.e., when a user accesses a non-existent route).
   * It renders a custom 404 error page using the Twig template `error404.html.twig`.
   * 
   * @return Response The rendered 404 error page.
   */
  #[Route('/error/404', name: 'app_error_404')]
  public function show404(): Response
  {
    // Render the 404 error page with a custom template and send a 404 HTTP status
    return $this->render('bundles/TwigBundle/Exception/error404.html.twig', [], new Response('', Response::HTTP_NOT_FOUND));
  }
}
