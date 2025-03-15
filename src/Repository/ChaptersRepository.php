<?php

namespace App\Repository;

use App\Entity\Chapters;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Chapters>
 */
class ChaptersRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Chapters::class);
  }

  public function findChaptersByLesson($id)
  {

    return $this->createQueryBuilder('c')
      ->join('c.lessonId', 'l')
      ->where('l.id = :id')
      ->setParameter('id', $id)
      ->getQuery()
      ->getResult();
  }

  //    /**
  //     * @return Chapters[] Returns an array of Chapters objects
  //     */
  //    public function findByExampleField($value): array
  //    {
  //        return $this->createQueryBuilder('c')
  //            ->andWhere('c.exampleField = :val')
  //            ->setParameter('val', $value)
  //            ->orderBy('c.id', 'ASC')
  //            ->setMaxResults(10)
  //            ->getQuery()
  //            ->getResult()
  //        ;
  //    }

  //    public function findOneBySomeField($value): ?Chapters
  //    {
  //        return $this->createQueryBuilder('c')
  //            ->andWhere('c.exampleField = :val')
  //            ->setParameter('val', $value)
  //            ->getQuery()
  //            ->getOneOrNullResult()
  //        ;
  //    }
}
