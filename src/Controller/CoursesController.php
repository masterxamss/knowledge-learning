<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CoursesController extends AbstractController
{
  #[Route('/courses/{slug}', name: 'app_courses')]
  public function index(string $slug): Response
  {
    return $this->render('courses/courses.html.twig', [
      'controller_name' => 'CoursesController',
    ]);
  }
}
