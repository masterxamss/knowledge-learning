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

/**
 * Controller for managing courses in the application.
 *
 * This controller is responsible for handling requests related to courses. It provides two main routes:
 * 1. `/courses`: A route that displays a list of all courses, with filtering options (such as theme, search term, price range, and badges).
 * 2. `/courses/{slug}`: A route that displays detailed information about a specific course, including lessons associated with that course.
 *
 * The controller ensures that users can view available courses, their associated themes, badges, and the lessons within each course. 
 * Additionally, it provides information about courses the user has already completed.
 *
 * The controller also handles error situations gracefully by providing flash messages in case of issues like empty results or exceptions.
 */
#[IsGranted('ROLE_USER')]
final class CoursesController extends AbstractController
{
  private CompletedCoursesService $completedCoursesService;

  // Constructor dependency injection for CompletedCoursesService
  public function __construct(CompletedCoursesService $completedCoursesService)
  {
    $this->completedCoursesService = $completedCoursesService;
  }

  /**
   * Retrieves all courses with filtering options such as theme, search term, price range, and badges.
   * 
   * This function performs the following:
   * 1. Retrieves all themes and badges from the database and handles the case where no themes or badges are found.
   * 2. Retrieves filter parameters (theme, search, price range, badges) from the request.
   * 3. Uses the `CoursesRepository` to find courses matching the filter criteria.
   * 4. Retrieves completed courses for the logged-in user using the `CompletedCoursesService`.
   * 5. Returns a response with the filtered courses, themes, badges, and completed courses to the view.
   *
   * @param Request $request The request object containing query parameters for filtering.
   * @param EntityManagerInterface $em The EntityManager used to interact with the database.
   * @return Response The response containing the view with the filtered courses and other data.
   */
  #[Route('/courses', name: 'app_courses', methods: ['GET'])]
  public function getAllCourses(Request $request, EntityManagerInterface $em): Response
  {
    try {
      // Retrieve all themes from the database
      $themes = $em->getRepository(Themes::class)->findAll();
      if (empty($themes)) {
        $this->addFlash('info', 'No themes found.');
      }

      // Retrieve all badges from the database
      $badges = $em->getRepository(Badges::class)->findAll();
      if (empty($badges)) {
        $this->addFlash('info', 'No badges found.');
      }

      // Get filtering parameters from the request query
      $themeName = $request->query->get('theme');
      $search = $request->query->get('search');
      $price = $request->query->get('price_range');
      $badgesId = $request->query->all('badges');

      // Find courses that match the filters
      $courses = $em->getRepository(Courses::class)
        ->findCoursesByFilters($themeName, $search, $price, $badgesId);

      if (empty($courses)) {
        $this->addFlash('info', 'No courses found.');
      }

      // Retrieve completed courses for the logged-in user
      $getCompletedCourses = $this->completedCoursesService->getCompletedCourses($this->getUser()->getId());

      // Render the view with courses, themes, badges, and completed courses
      return $this->render('courses/courses.html.twig', [
        'courses' => $courses,
        'themes' => $themes,
        'badges' => $badges,
        'completedCourses' => $getCompletedCourses
      ]);
    } catch (\Exception $e) {
      // Catch any errors and show an error message
      $this->addFlash('error', 'An error occurred: ' . $e->getMessage());
      return $this->redirectToRoute('app_home');
    }
  }

  /**
   * Displays details of a specific course identified by its slug.
   * 
   * This function:
   * 1. Retrieves the course by its slug.
   * 2. Retrieves all lessons associated with the course.
   * 3. If no lessons are found, adds a flash message.
   * 4. Retrieves completed courses for the logged-in user.
   * 5. Returns a response with the course details and lessons to the view.
   *
   * @param Request $request The request object containing the course slug.
   * @param EntityManagerInterface $em The EntityManager used to interact with the database.
   * @return Response The response containing the view with the course details and lessons.
   */
  #[Route('/courses/{slug}', name: 'app_course_show', methods: ['GET'])]
  public function getCourse(Request $request, EntityManagerInterface $em): Response
  {
    try {
      // Get course slug from the URL
      $slug = $request->attributes->get('slug');
      $course = $em->getRepository(Courses::class)->findOneBy(['slug' => $slug]);

      if (empty($course)) {
        return $this->redirectToRoute('app_courses');
      }

      // Retrieve lessons for the course
      $lessons = $em->getRepository(Lessons::class)->findLessonsByCourse($course->getId());
      if (empty($lessons)) {
        $this->addFlash('info', 'No lessons found for this course.');
      }

      // Retrieve completed courses for the logged-in user
      $getCompletedCourses = $this->completedCoursesService->getCompletedCourses($this->getUser()->getId());

      // Render the view with the course and lessons
      return $this->render('courses/course.html.twig', [
        'course' => $course,
        'lessons' => $lessons,
        'completedCourses' => $getCompletedCourses
      ]);
    } catch (\Exception $e) {
      // Catch any errors and show an error message
      $this->addFlash('error', 'An error occurred: ' . $e->getMessage());
      return $this->redirectToRoute('app_courses');
    }
  }
}
