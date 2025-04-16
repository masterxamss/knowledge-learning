<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Courses;
use App\Entity\Lessons;

use App\Entity\Completion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Completion>
 */
class CompletionRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Completion::class);
  }

  public function findCompletionsLessonByUser($userId, $courseId)
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

  public function getCourseCompletionPercentage(User $user, Courses $course): int
  {
    $totalLessons = $this->getEntityManager()->getRepository(Lessons::class)
      ->count(['course' => $course]);

    if ($totalLessons === 0) {
      return 0;
    }

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

    return round(($completedLessons / $totalLessons) * 100);
  }
}
