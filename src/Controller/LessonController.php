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

/**
 * Controller responsible for managing the lesson views, marking lessons as completed, and generating certifications.
 *
 * This controller handles the logic for viewing lessons, marking them as completed or in progress, 
 * and checking if a user has completed a course. It also manages the generation of certifications for courses 
 * when a user completes all lessons in the course. It uses custom access control via a `LessonVoter` to 
 * ensure that users can only access lessons they are authorized to view.
 */
final class LessonController extends AbstractController
{
  /**
   * Displays the requested lesson, along with its chapters and progress.
   * 
   * This method checks if the requested lesson exists and is accessible to the current user. 
   * It also retrieves the chapters associated with the lesson and marks a chapter's video if available.
   * The user's progress in the lesson is also shown. If there are any issues, flash messages are displayed.
   *
   * @param string $slug The slug of the lesson.
   * @param EntityManagerInterface $em The EntityManager to interact with the database.
   * @param Request $request The current request object.
   * @param string|null $chapterSlug The slug of the chapter to display, if provided.
   * 
   * @return Response The rendered lesson page.
   */
  #[Route('/lesson/{slug}/{chapterSlug?}', name: 'app_lesson', methods: ['GET'])]
  public function getLesson(string $slug, EntityManagerInterface $em, Request $request, ?string $chapterSlug): Response
  {
    $referer = $request->headers->get('referer');
    try {
      // Fetch the lesson based on the slug
      $lesson = $em->getRepository(Lessons::class)->findOneBy(['slug' => $slug]);

      if (!$lesson) {
        // Lesson not found, redirect back or to home
        $this->addFlash('info', 'La leçon n\'existe pas');
        return $referer ? $this->redirect($referer) : $this->redirectToRoute('app_home');
      }

      $course = $lesson->getCourse();

      // Check if the user has access to view the lesson
      $this->denyAccessUnlessGranted(LessonVoter::VIEW, ['lesson' => $lesson, 'course' => $course]);

      // Fetch the chapters of the lesson
      $chapters = $em->getRepository(Chapters::class)->findBy(['lessonId' => $lesson->getId()]);

      if (!$chapters) {
        $this->addFlash('info', 'Chapitres non trouvés');
        return $referer ? $this->redirect($referer) : $this->redirectToRoute('app_home');
      }

      $selectedChapter = null;
      $videoId = null;

      // If a chapter slug is provided, find the corresponding chapter and extract the video ID if available
      if ($chapterSlug) {
        $selectedChapter = $em->getRepository(Chapters::class)->findOneBy(['slug' => $chapterSlug, 'lessonId' => $lesson->getId()]);
        if ($selectedChapter && $selectedChapter->getVideo()) {
          preg_match('/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|v\/|.+\?v=))([^&]+)/', $selectedChapter->getVideo(), $matches);
          $videoId = $matches[1] ?? null;
        }
      }

      // Check if the user has completed the lesson
      $lessonCompletion = $em->getRepository(Completion::class)->findBy(['lesson' => $lesson->getId(), 'user' => $this->getUser()]);

      // Render the lesson page with all relevant data
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

  /**
   * Marks the lesson as completed or in-progress based on the user input.
   * 
   * This method handles the logic for updating the lesson completion status, 
   * as well as managing certifications and course completion checks.
   * If a user completes all lessons in a course, they will receive a certification.
   *
   * @param int $id The ID of the lesson.
   * @param EntityManagerInterface $em The EntityManager to interact with the database.
   * @param Request $request The current request object.
   * 
   * @return Response The redirected response after processing the lesson completion.
   */
  #[Route('lesson/completed/{id}', name: 'app_lesson_completed', methods: ['POST'])]
  public function setLessonCompleted(int $id, EntityManagerInterface $em, Request $request): Response
  {
    try {
      // Fetch the lesson based on its ID
      $lesson = $em->getRepository(Lessons::class)->find($id);
      if (!$lesson) {
        throw new \Exception('Lição não encontrada.');
      }

      $course = $lesson->getCourse();
      $user = $this->getUser();

      // Check if the user has access to the lesson
      $this->denyAccessUnlessGranted(LessonVoter::VIEW, ['lesson' => $lesson, 'course' => $course]);

      // Get or create the lesson completion record
      $lessonCompletion = $this->getLessonCompletion($em, $lesson, $user);
      if (!$lessonCompletion) {
        $this->addFlash('info', 'Le progress pour cette leçon n\'existe pas');
        return $this->redirectToRoute('app_lesson', ['slug' => $lesson->getSlug()]);
      }

      // Update the lesson completion status
      $status = $request->get('completed');
      if ($status === 'in-progress') {
        $this->markLessonAsCompleted($lessonCompletion[0], $em);
      } else {
        $this->markLessonAsInProgress($lessonCompletion[0], $em);
        $this->removeCertification($em, $user, $course);
      }

      // Check if the course is completed
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
  private function getLessonCompletion(EntityManagerInterface $em, Lessons $lesson, $user)
  {
    return $em->getRepository(Completion::class)->findBy([
      'user' => $user->getId(),
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
