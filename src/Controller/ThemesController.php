<?php

namespace App\Controller;

use App\Entity\Themes;
use App\Entity\Courses;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Controller responsible for displaying themes and associated courses.
 */
final class ThemesController extends AbstractController
{
  /**
   * Displays a list of themes and the number of courses associated with each theme.
   * 
   * This method retrieves all themes from the database and counts the number of courses related to each theme.
   * It then renders a page displaying the themes and the number of courses in each theme.
   * 
   * @param EntityManagerInterface $em The Doctrine entity manager for interacting with the database.
   * 
   * @return Response The HTTP response containing the rendered themes page.
   */
  #[Route('/themes', name: 'app_themes')]
  public function index(EntityManagerInterface $em): Response
  {
    try {
      // Retrieve all themes from the database
      $themes = $em->getRepository(Themes::class)->findAll();

      // Initialize an array to store the number of courses per theme
      $themeCoursesCounts = [];

      // Loop through each theme and calculate the number of associated courses
      foreach ($themes as $theme) {
        $courses = $em->getRepository(Courses::class)->findCoursesByTheme($theme->getId());
        $themeCoursesCounts[$theme->getId()] = count($courses);
      }

      // If no themes were found, show an info flash message
      if (empty($themes)) {
        $this->addFlash('info', 'No themes found.');
      }

      // Render the themes page with the themes and their course counts
      return $this->render('themes/themes.html.twig', [
        'themes' => $themes,
        'themeCoursesCounts' => $themeCoursesCounts
      ]);
    } catch (\Exception $e) {
      // Handle any exceptions and show an error message
      $this->addFlash('error', 'An error occurred: ' . $e->getMessage());

      // Render the themes page with null values in case of an error
      return $this->render('themes/themes.html.twig', [
        'themes' => null,
        'themeCoursesCounts' => null
      ]);
    }
  }
}
