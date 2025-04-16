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
use App\Entity\Completion;
use App\Entity\Certifications;

final class LessonController extends AbstractController
{
  #[Route('/lesson/{slug}/{chapterSlug?}', name: 'app_lesson', methods: ['GET'])]
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

        // Extract the YouTube video ID from the URL (if it exists)
        if ($selectedChapter && $selectedChapter->getVideo()) {
          preg_match('/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|v\/|.+\?v=))([^&]+)/', $selectedChapter->getVideo(), $matches);
          $videoId = $matches[1] ?? null;
        }
      }

      $lessonCompletion = $em->getRepository(Completion::class)->findBy(['lesson' => $lesson->getId()]);

      return $this->render('lesson/lesson.html.twig', [
        'lesson' => $lesson,
        'chapters' => $chapters,
        'selectedChapter' => $selectedChapter,
        'lessonCompletion' => $lessonCompletion,
        'videoId' => $videoId
      ]);
    } catch (\Exception $e) {
      $this->addFlash('error', $e->getMessage());
      return $this->redirectToRoute('app_course_show', ['slug' => $course->getSlug()]);
    }
  }

  #[Route('lesson/completed/{id}', name: 'app_lesson_completed', methods: ['POST'])]
  public function setLessonCompleted(int $id, EntityManagerInterface $em, Request $request): Response
  {
    try {
      $lesson = $em->getRepository(Lessons::class)->find($id);
      if (!$lesson) {
        throw new \Exception('Lição não encontrada.');
      }

      $course = $lesson->getCourse();
      $user = $this->getUser();

      $this->denyAccessUnlessGranted(LessonVoter::VIEW, [
        'lesson' => $lesson,
        'course' => $course
      ]);

      $lessonCompletion = $this->getLessonCompletion($em, $lesson);

      if (!$lessonCompletion) {
        $this->addFlash('info', 'Le progress pour cette leçon n\'existe pas');
        return $this->redirectToRoute('app_lesson', ['slug' => $lesson->getSlug()]);
      }

      $status = $request->get('completed');

      if ($status === 'in-progress') {
        $this->markLessonAsCompleted($lessonCompletion[0], $em);
      } else {
        $this->markLessonAsInProgress($lessonCompletion[0], $em);
        $this->removeCertification($em, $user, $course);
      }

      // Verify if the course is completed
      if ($this->checkCourseCompletion($em, $course, $user)) {
        $this->addFlash('course_completed', 'Felicitations 🎉');
        $this->generateCertificationIfNeeded($user, $course, $em);
      }

      return $this->redirectToRoute('app_lesson', ['slug' => $lesson->getSlug()]);
    } catch (\Exception $e) {
      $this->addFlash('error', 'Error: ' . $e->getMessage());
      return $this->redirectToRoute('app_home');
    }
  }

  // Method to get the progress of the lesson
  private function getLessonCompletion(EntityManagerInterface $em, Lessons $lesson)
  {
    return $em->getRepository(Completion::class)->findBy([
      'lesson' => $lesson->getId()
    ]);
  }

  // Method to mark the lesson as completed
  private function markLessonAsCompleted(Completion $completion, EntityManagerInterface $em): void
  {
    $completion->setStatus('completed');
    $completion->setCompletionDate(new \DateTimeImmutable());
    $em->persist($completion);
    $em->flush();
  }

  // Method to mark the lesson as "in progress"
  private function markLessonAsInProgress(Completion $completion, EntityManagerInterface $em): void
  {
    $completion->setStatus('in-progress');
    $completion->setCompletionDate(null);
    $em->persist($completion);
    $em->flush();
  }

  // Method to check if the course is completed
  private function checkCourseCompletion(EntityManagerInterface $em, $course, $user): bool
  {
    $lessonsByCourse = $em->getRepository(Lessons::class)->findLessonsByCourse($course->getId());
    $lessonsUserCompleted = $em->getRepository(Completion::class)->findCompletionsLessonByUser($user->getId(), $course->getId());

    return count($lessonsUserCompleted) === count($lessonsByCourse);
  }

  // Method to generate a certification if necessary
  private function generateCertificationIfNeeded($user, $course, EntityManagerInterface $em): void
  {
    $certifications = $em->getRepository(Certifications::class)
      ->findBy(['user' => $user->getId(), 'course' => $course->getId()]);

    if (!$certifications) {
      $certification = new Certifications();
      $certification->setUser($user);
      $certification->setCourse($course);
      $certification->setDate(new \DateTimeImmutable());
      $em->persist($certification);
      $em->flush();
    }
  }

  // Remove certification if completion is unmarked
  private function removeCertification(EntityManagerInterface $em, $user, $course): void
  {
    $certifications = $em->getRepository(Certifications::class)
      ->findBy(['user' => $user->getId(), 'course' => $course->getId()]);

    if ($certifications) {
      $em->remove($certifications[0]);
      $em->flush();
    }
  }
}
