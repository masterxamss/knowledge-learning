<?php

namespace App\Controller;

use App\Entity\Courses;
use App\Entity\Themes;
use App\Entity\Lessons;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

class HomeController extends AbstractController
{
  #[Route('/', name: 'app_home')]
  public function index(EntityManagerInterface $em): Response
  {
    $themes = null;
    $courses = null;
    $lessons = null;

    $entities = [
      'themes' => Themes::class,
      'courses' => Courses::class,
      'lessons' => Lessons::class
    ];

    try {

      foreach ($entities as $key => $entityClass) {
        $results = $em->getRepository($entityClass)->findBy(['highlight' => true]);
        if ($key === 'themes') {
          $themes = $results;
        } elseif ($key === 'courses') {
          $courses = $results;
        } elseif ($key === 'lessons') {
          $lessons = $results;
        }

        if (empty($results)) {
          $this->addFlash('info', "Aucun" . $key . " n\'a été trouvé.");
        }
      }

      return $this->render('home/home.html.twig', [
        'courses' => $courses,
        'themes' => $themes,
        'lessons' => $lessons
      ]);
    } catch (\Exception) {
      $this->addFlash('error', 'Une erreur est survenue');
      return $this->render('home/home.html.twig', [
        'courses' => null,
        'themes' => null,
        'lessons' => null
      ]);
    }
  }
}
