<?php

namespace App\Repository;

use App\Entity\Lessons;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for interacting with the Lessons entity.
 *
 * This repository class provides methods for querying lessons based on the course ID.
 *
 * @extends ServiceEntityRepository<Lessons>
 */
class LessonsRepository extends ServiceEntityRepository
{
  /**
   * Constructor.
   *
   * Initializes the repository with the ManagerRegistry and the Lessons entity class.
   *
   * @param ManagerRegistry $registry The manager registry.
   */
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Lessons::class);
  }

  /**
   * Finds lessons by a specific course ID.
   *
   * This method retrieves all lessons associated with a specific course ID.
   *
   * @param int $id The ID of the course to filter lessons by.
   *
   * @return Lessons[] An array of Lessons entities related to the specified course.
   */
  public function findLessonsByCourse(int $id): array
  {
    return $this->createQueryBuilder('l')
      ->join('l.course', 'c')
      ->where('c.id = :id')
      ->setParameter('id', $id)
      ->getQuery()
      ->getResult();
  }
}
