<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Security\Voter\LessonVoter;
use App\Entity\Lessons;
use App\Entity\Chapters;

final class LessonController extends AbstractController
{
  #[Route('/lesson/{slug}/{chapterSlug?}', name: 'app_lesson')]
  public function getLesson(string $slug, EntityManagerInterface $em, Request $request, ?string $chapterSlug): Response
  {
    $referer = $request->headers->get('referer');
    try {
      $lesson = $em->getRepository(Lessons::class)->findOneBy(['slug' => $slug]);

      if (!$lesson) {
        $this->addFlash('info', 'La leçon n\'existe pas');
        return $referer ? $this->redirect($referer) : $this->redirectToRoute('app_home');
      }

      $course = $lesson->getCourse();

      $this->denyAccessUnlessGranted(LessonVoter::VIEW, ['lesson' => $lesson, 'course' => $course]);

      $chapters = $em->getRepository(Chapters::class)->findBy(['lessonId' => $lesson->getId()]);

      if (!$chapters) {
        $this->addFlash('info', 'Chapitres non trouvés');
        $referer = $request->headers->get('referer');

        return $referer ? $this->redirect($referer) : $this->redirectToRoute('app_home');
      }

      $selectedChapter = null;
      $videoId = null;

      if ($chapterSlug) {
        $selectedChapter = $em->getRepository(Chapters::class)->findOneBy(['slug' => $chapterSlug, 'lessonId' => $lesson->getId()]);

        // Extrair o ID do vídeo do YouTube a partir da URL (se existir)
        if ($selectedChapter && $selectedChapter->getVideo()) {
          preg_match('/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|v\/|.+\?v=))([^&]+)/', $selectedChapter->getVideo(), $matches);
          $videoId = $matches[1] ?? null;
        }
      }

      return $this->render('lesson/lesson.html.twig', [
        'lesson' => $lesson,
        'chapters' => $chapters,
        'selectedChapter' => $selectedChapter,
        'videoId' => $videoId
      ]);
    } catch (\Exception $e) {
      $this->addFlash('error', $e->getMessage());
      return $this->redirectToRoute('app_course_show', ['slug' => $course->getSlug()]);
    }
  }
}
