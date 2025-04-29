<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Courses;
use App\Entity\Lessons;
use App\Entity\Completion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for interacting with the Completion entity.
 *
 * This repository class provides methods for querying completion records related to users and courses,
 * as well as calculating the completion percentage for a course.
 *
 * @extends ServiceEntityRepository<Completion>
 */
class CompletionRepository extends ServiceEntityRepository
{
  /**
   * Constructor.
   *
   * Initializes the repository with the ManagerRegistry and the Completion entity class.
   *
   * @param ManagerRegistry $registry The manager registry.
   */
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Completion::class);
  }

  /**
   * Finds completions for a specific lesson by a user and course.
   *
   * This method retrieves the completions for a specific lesson, filtering by user ID, course ID,
   * and completion status (i.e., 'completed').
   *
   * @param int $userId The ID of the user for whom the completions are to be found.
   * @param int $courseId The ID of the course for which the lesson completions are to be found.
   *
   * @return Completion[] An array of Completion entities related to the specified user, course, and lesson.
   */
  public function findCompletionsLessonByUser(int $userId, int $courseId): array
  {
    return $this->createQueryBuilder('c')
      ->join('c.user', 'u')
      ->join('c.lesson', 'l')
      ->join('l.course', 'course')
      ->where('u.id = :userId')
      ->andWhere('course.id = :courseId')
      ->andWhere('c.status = :status')
      ->setParameter('userId', $userId)
      ->setParameter('courseId', $courseId)
      ->setParameter('status', 'completed')
      ->getQuery()
      ->getResult();
  }

  /**
   * Calculates the completion percentage of a course for a given user.
   *
   * This method calculates the percentage of lessons completed by a user in a specific course.
   * It counts the total lessons in the course and the completed lessons for the user and then
   * computes the percentage.
   *
   * @param User $user The user whose course completion percentage is to be calculated.
   * @param Courses $course The course for which the completion percentage is to be calculated.
   *
   * @return int The completion percentage (0-100) of the course for the given user.
   */
  public function getCourseCompletionPercentage(User $user, Courses $course): int
  {
    // Get the total number of lessons in the course.
    $totalLessons = $this->getEntityManager()->getRepository(Lessons::class)
      ->count(['course' => $course]);

    // If there are no lessons in the course, return 0%.
    if ($totalLessons === 0) {
      return 0;
    }

    // Get the number of completed lessons for the user in this course.
    $completedLessons = $this->createQueryBuilder('c')
      ->select('COUNT(DISTINCT l.id)')
      ->innerJoin('c.lesson', 'l')
      ->where('c.user = :user')
      ->andWhere('l.course = :course')
      ->andWhere('c.status = :status')
      ->setParameter('status', 'completed')
      ->setParameter('user', $user)
      ->setParameter('course', $course)
      ->getQuery()
      ->getSingleScalarResult();

    // Calculate and return the completion percentage.
    return round(($completedLessons / $totalLessons) * 100);
  }
}
