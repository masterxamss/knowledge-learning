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
  //    /**
  //     * @return Lessons[] Returns an array of Lessons objects
  //     */
  //    public function findByExampleField($value): array
  //    {
  //        return $this->createQueryBuilder('l')
  //            ->andWhere('l.exampleField = :val')
  //            ->setParameter('val', $value)
  //            ->orderBy('l.id', 'ASC')
  //            ->setMaxResults(10)
  //            ->getQuery()
  //            ->getResult()
  //        ;
  //    }

  //    public function findOneBySomeField($value): ?Lessons
  //    {
  //        return $this->createQueryBuilder('l')
  //            ->andWhere('l.exampleField = :val')
  //            ->setParameter('val', $value)
  //            ->getQuery()
  //            ->getOneOrNullResult()
  //        ;
  //    }
}
