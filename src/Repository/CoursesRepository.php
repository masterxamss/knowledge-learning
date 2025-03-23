<?php

namespace App\Repository;

use App\Entity\Courses;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Courses>
 */
class CoursesRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Courses::class);
  }

  public function findCoursesByTheme($id)
  {
    return $this->createQueryBuilder('c')
      ->join('c.theme', 't')
      ->where('t.id = :id')
      ->setParameter('id', $id)
      ->getQuery()
      ->getResult();
  }

  public function findCoursesByFilters(?string $themeName, ?string $search, ?string $priceRange, array $badges): array
  {
    $qb = $this->createQueryBuilder('c');

    if (!empty($badges)) {
      $qb->join('c.badge', 'b')
        ->andwhere('b.id IN (:badges)')
        ->setParameter('badges', $badges);
    }

    if (!empty($themeName)) {
      $qb->join('c.theme', 't')
        ->andwhere('t.name = :name')
        ->setParameter('name', $themeName);
    }

    if (!empty($search)) {
      $qb->andWhere('c.title LIKE :search')
        ->setParameter('search', '%' . $search . '%');
    }

    if ($priceRange) {
      switch ($priceRange) {
        case '30-40':
          $qb->andWhere('c.price BETWEEN :min AND :max')
            ->setParameter('min', 30)
            ->setParameter('max', 40);
          break;
        case '40-50':
          $qb->andWhere('c.price BETWEEN :min AND :max')
            ->setParameter('min', 40)
            ->setParameter('max', 50);
          break;
        case '50-60':
          $qb->andWhere('c.price BETWEEN :min AND :max')
            ->setParameter('min', 50)
            ->setParameter('max', 60);
          break;
      }
    }
    return $qb->getQuery()->getResult();
  }
}
