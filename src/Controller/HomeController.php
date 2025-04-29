<?php

namespace App\Controller;

use App\Entity\Courses;
use App\Entity\Themes;
use App\Entity\Lessons;
use App\Service\CompletedCoursesService;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Controller responsible for managing the home page and displaying highlighted themes, courses, and lessons.
 *
 * This controller handles the logic for the home page by fetching the highlighted themes, courses, and lessons
 * from the database and passing them to the view. It also retrieves the list of completed courses for the logged-in user.
 * The controller uses a service (`CompletedCoursesService`) to get the completed courses and manages any errors that may occur.
 */
class HomeController extends AbstractController
{
  private CompletedCoursesService $completedCoursesService;

  /**
   * Constructor to inject the CompletedCoursesService dependency.
   *
   * @param CompletedCoursesService $completedCoursesService
   */
  public function __construct(CompletedCoursesService $completedCoursesService)
  {
    $this->completedCoursesService = $completedCoursesService;
  }

  /**
   * Route to display the home page with highlighted themes, courses, and lessons.
   * 
   * This method retrieves the themes, courses, and lessons that are marked as highlighted and renders them on the home page.
   * If the user is logged in, it also retrieves the list of completed courses for that user.
   * If any of the entities (themes, courses, lessons) are empty, a flash message is displayed.
   * 
   * @param EntityManagerInterface $em The EntityManager to interact with the database.
   * 
   * @return Response The rendered home page with the necessary data.
   */
  #[Route('/', name: 'app_home')]
  public function index(EntityManagerInterface $em): Response
  {
    $themes = null;
    $courses = null;
    $lessons = null;
    $getCompletedCourses = null;

    $entities = [
      'themes' => Themes::class,
      'courses' => Courses::class,
      'lessons' => Lessons::class
    ];

    try {
      // Fetch highlighted entities (themes, courses, lessons) from the database
      foreach ($entities as $key => $entityClass) {
        $results = $em->getRepository($entityClass)->findBy(['highlight' => true]);
        if ($key === 'themes') {
          $themes = $results;
        } elseif ($key === 'courses') {
          $courses = $results;
        } elseif ($key === 'lessons') {
          $lessons = $results;
        }

        // Display a flash message if no results are found for an entity
        if (empty($results)) {
          $this->addFlash('info', "Aucun " . $key . " trouvé.");
        }
      }

      // Retrieve completed courses for the logged-in user, if applicable
      if ($this->getUser()) {
        $userId = $this->getUser()->getId();
        $getCompletedCourses = $this->completedCoursesService->getCompletedCourses($userId);
      }

      // Render the home page with the fetched data
      return $this->render('home/home.html.twig', [
        'courses' => $courses,
        'themes' => $themes,
        'lessons' => $lessons,
        'completedCourses' => $getCompletedCourses
      ]);
    } catch (\Exception $e) {
      // Handle errors by displaying a flash message and rendering the home page with null data
      $this->addFlash('error', 'Une erreur est survenue' . $e->getMessage());
      return $this->render('home/home.html.twig', [
        'courses' => null,
        'themes' => null,
        'lessons' => null,
        'completedCourses' => null
      ]);
    }
  }
}
