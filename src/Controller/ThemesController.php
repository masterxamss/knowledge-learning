<?php

namespace App\Controller;

use App\Entity\Themes;
use App\Entity\Courses;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


final class ThemesController extends AbstractController
{
  #[Route('/themes', name: 'app_themes')]
  public function index(EntityManagerInterface $em): Response
  {
    try {
      $themes = $em->getRepository(Themes::class)->findAll();

      $themeCoursesCounts = [];

      foreach ($themes as $theme) {
        $courses = $em->getRepository(Courses::class)->findCoursesByTheme($theme->getId());
        $themeCoursesCounts[$theme->getId()] = count($courses);
      }

      if (empty($themes)) {
        $this->addFlash('info', 'Aucun thème n\'a été trouvée.');
      }

      return $this->render('themes/themes.html.twig', [
        'themes' => $themes,
        'themeCoursesCounts' => $themeCoursesCounts
      ]);
    } catch (\Exception $e) {
      $this->addFlash('error', 'Une erreur est survenue' . $e->getMessage());
      return $this->render('themes/themes.html.twig', [
        'themes' => null,
        'themeCoursesCounts' => null
      ]);
    }
  }
}
