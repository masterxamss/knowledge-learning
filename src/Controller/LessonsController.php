<?php

namespace App\Controller;

use App\Entity\Lessons;
use App\Entity\Chapters;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LessonsController extends AbstractController
{
  #[Route('/lessons/{slug}', name: 'app_lessons')]
  public function getLessons(string $slug, EntityManagerInterface $em): Response
  {

    // TODO: check if lesson, chapter exists, try catch block

    $lesson = $em->getRepository(Lessons::class)->findOneBy(['slug' => $slug]);
    $videoId = $this->extractVideoId($lesson->getVideo());
    $chapters = $em->getRepository(Chapters::class)->findChaptersByLesson($lesson->getId());

    return $this->render('lessons/lessons.html.twig', [
      'lesson' => $lesson,
      'videoId' => $videoId,
      'chapters' => $chapters,
      'chapter' => null
    ]);
  }

  #[Route('/lessons/{slugLesson}/{slugChapter}', name: 'app_chapters')]
  public function getChapter(string $slugLesson, string $slugChapter, EntityManagerInterface $em): Response
  {

    // TODO: check if lesson, chapter exists, try catch block


    $lesson = $em->getRepository(Lessons::class)->findOneBy(['slug' => $slugLesson]);
    $videoId = $this->extractVideoId($lesson->getVideo());
    $chapters = $em->getRepository(Chapters::class)->findChaptersByLesson($lesson->getId());


    $chapter = $em->getRepository(Chapters::class)->findOneBy(['slug' => $slugChapter]);

    return $this->render('lessons/lessons.html.twig', [
      'lesson' => $lesson,
      'videoId' => $videoId,
      'chapters' => $chapters,
      'chapter' => $chapter
    ]);
  }

  private function extractVideoId(string $url): ?string
  {
    // Regular expression to match YouTube video ID
    if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $matches)) {
      return $matches[1];
    }

    return null;
  }
}
