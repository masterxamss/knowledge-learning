<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ErrorController extends AbstractController
{
  #[Route('/error/404', name: 'app_error_404')]
  public function show404(): Response
  {
    return $this->render('bundles/TwigBundle/Exception/error404.html.twig', [], new Response('', Response::HTTP_NOT_FOUND));
  }
}
