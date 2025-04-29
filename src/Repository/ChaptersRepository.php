<?php

namespace App\Repository;

use App\Entity\Chapters;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for interacting with the Chapters entity.
 *
 * This repository class provides methods for querying chapters related to lessons
 * and performing other database operations on the Chapters entity.
 *
 * @extends ServiceEntityRepository<Chapters>
 */
class ChaptersRepository extends ServiceEntityRepository
{
  /**
   * Constructor.
   *
   * Initializes the repository with the ManagerRegistry and the Chapters entity class.
   *
   * @param ManagerRegistry $registry The manager registry.
   */
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Chapters::class);
  }
  /**
   * Finds chapters associated with a specific lesson.
   *
   * This method retrieves all chapters linked to a given lesson by its ID.
   * It uses a query builder to join the Chapters entity with the Lesson entity
   * and filters based on the provided lesson ID.
   *
   * @param int $id The ID of the lesson for which chapters are to be found.
   *
   * @return Chapters[] An array of Chapters entities related to the specified lesson.
   */
  public function findChaptersByLesson($id)
  {

    return $this->createQueryBuilder('c')
      ->join('c.lessonId', 'l')
      ->where('l.id = :id')
      ->setParameter('id', $id)
      ->getQuery()
      ->getResult();
  }
}
