<?php

namespace App\Repository;

use App\Entity\Lessons;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Lessons>
 */
class LessonsRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Lessons::class);
  }

  public function findLessonsByCourse($id)
  {
    return $this->createQueryBuilder('l')
      ->join('l.course', 'c')
      ->where('c.id = :id')
      ->setParameter('id', $id)
      ->getQuery()
      ->getResult();
  }
}
