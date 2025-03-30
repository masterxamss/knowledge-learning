<?php

namespace App\Controller;

use App\Entity\Courses;
use App\Entity\Themes;
use App\Entity\Badges;
use App\Entity\Lessons;
use App\Service\CompletedCoursesService;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class CoursesController extends AbstractController
{
  private CompletedCoursesService $completedCoursesService;

  public function __construct(CompletedCoursesService $completedCoursesService)
  {
    $this->completedCoursesService = $completedCoursesService;
  }

  #[Route('/courses', name: 'app_courses', methods: ['GET'])]
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

      $userId = $this->getUser()->getId();
      $getCompletedCourses = $this->completedCoursesService->getCompletedCourses($userId);

      return $this->render('courses/courses.html.twig', [
        'courses' => $courses,
        'themes' => $themes,
        'badges' => $badges,
        'completedCourses' => $getCompletedCourses
      ]);
    } catch (\Exception $e) {
      $this->addFlash('error', 'Une erreur est survenue: ' . $e->getMessage());
      return $this->render('courses/courses.html.twig', [
        'courses' => null,
        'themes' => null,
        'badges' => null,
        'completedCourses' => null
      ]);
    }
  }


  #[Route('/courses/{slug}', name: 'app_course_show', methods: ['GET'])]
  public function getCourse(Request $request, EntityManagerInterface $em): Response
  {
    try {
      $slug = $request->attributes->get('slug');
      $course = $em->getRepository(Courses::class)->findOneBy(['slug' => $slug]);
      if (empty($course)) {
        $this->addFlash('info', 'Aucun cours trouvé');
      }

      $lessons = $em->getRepository(Lessons::class)->findLessonsByCourse($course->getId());
      if (empty($lessons)) {
        $this->addFlash('info', 'Aucune leçon trouvée');
      }

      $userId = $this->getUser()->getId();
      $getCompletedCourses = $this->completedCoursesService->getCompletedCourses($userId);
      return $this->render('courses/course.html.twig', [
        'course' => $course,
        'lessons' => $lessons,
        'completedCourses' => $getCompletedCourses
      ]);
    } catch (\Exception $e) {
      $this->addFlash('error', 'Une erreur est survenue: ' . $e->getMessage());
      return $this->render('courses/course.html.twig', [
        'course' => null,
        'lessons' => null,
        'completedCourses' => null
      ]);
    }
  }
}
