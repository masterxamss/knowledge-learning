<?php

namespace App\Controller;

use App\Entity\Courses;
use App\Entity\Lessons;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

final class CoursesController extends AbstractController
{
  #[Route('/courses/{slug}', name: 'app_courses')]
  public function index(string $slug, EntityManagerInterface $em): Response
  {
    try {
      // Get course
      $getCourse = $em->getRepository(Courses::class)->findBy(['slug' => $slug]);

      // Return lessons related to course
      foreach ($getCourse as $course) {
        $lessons = $em->getRepository(Lessons::class)->findBy(['course' => $course->getId()]);
      }

      // Render template
      return $this->render('courses/courses.html.twig', [
        'course' => $getCourse,
        'lessons' => $lessons
      ]);
    } catch (\Exception $e) {
      // Handle error
      $this->addFlash('error', 'Une erreur est survenue' . $e->getMessage());
      return $this->redirectToRoute('app_home');
    }
  }
}
