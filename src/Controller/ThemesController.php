<?php

namespace App\Controller;

use App\Entity\Courses;
use App\Entity\Lessons;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class ThemesController extends AbstractController
{
  #[Route('/themes/{slug}', name: 'app_categories')]
  public function index(string $slug, EntityManagerInterface $em): Response
  {
    try {
      // Return courses related to theme
      // dd($slug);
      $courses = $em->getRepository(Courses::class)->findCoursesByTheme($slug);

      // Return lessons related to course
      $data = [];
      foreach ($courses as $course) {
        $data[] = [
          'course' => $course,
          'lessons' => $em->getRepository(Lessons::class)->findBy(['course' => $course->getId()])
        ];
      }

      // Check if there are courses
      if (!$courses) {
        $this->addFlash('error', 'Il n\'y a pas de cours connexes');
        return $this->redirectToRoute('app_home');
      }

      // Render page
      return $this->render('themes/themes.html.twig', [
        'courses' => $courses,
        'coursesData' => $data
      ]);
    } catch (\Exception $e) {
      // Handle error
      $this->addFlash('error', 'Une erreur est survenue' . $e->getMessage());
      return $this->redirectToRoute('app_home');
    }
  }
}
