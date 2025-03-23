<?php

namespace App\Controller;

use App\Entity\Courses;
use App\Entity\Themes;
use App\Entity\Badges;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class CoursesController extends AbstractController
{
  #[Route('/courses', name: 'app_courses')]
  public function getAllCourses(Request $request, EntityManagerInterface $em): Response
  {
    try {
      $themes = $em->getRepository(Themes::class)->findAll();
      if (empty($themes)) {
        $this->addFlash('info', 'Aucun thème trouvé');
      }

      $badges = $em->getRepository(Badges::class)->findAll();
      if (empty($badges)) {
        $this->addFlash('info', 'Aucun badge trouvé');
      }

      $themeName = $request->query->get('theme');
      $search = $request->query->get('search');
      $price = $request->query->get('price_range');
      $badgesId = $request->query->all('badges');

      $courses = $em->getRepository(Courses::class)
        ->findCoursesByFilters($themeName, $search, $price, $badgesId);

      if (empty($courses)) {
        $this->addFlash('info', 'Aucun cours trouvé');
      }

      return $this->render('courses/courses.html.twig', [
        'courses' => $courses,
        'themes' => $themes,
        'badges' => $badges
      ]);
    } catch (\Exception $e) {
      $this->addFlash('error', 'Une erreur est survenue: ' . $e->getMessage());
      return $this->render('courses/courses.html.twig', [
        'courses' => null,
        'themes' => null,
        'badges' => null
      ]);
    }
  }
}
