<?php

namespace App\Repository;

use App\Entity\Courses;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for interacting with the Courses entity.
 *
 * This repository class provides methods for querying courses based on various filters,
 * such as theme, badges, title search, and price range.
 *
 * @extends ServiceEntityRepository<Courses>
 */ class CoursesRepository extends ServiceEntityRepository
{
  /**
   * Constructor.
   *
   * Initializes the repository with the ManagerRegistry and the Courses entity class.
   *
   * @param ManagerRegistry $registry The manager registry.
   */
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Courses::class);
  }

  /**
   * Finds courses by a specific theme ID.
   *
   * This method retrieves all courses associated with a specific theme ID.
   *
   * @param int $id The ID of the theme to filter courses by.
   *
   * @return Courses[] An array of Courses entities related to the specified theme.
   */
  public function findCoursesByTheme($id)
  {
    return $this->createQueryBuilder('c')
      ->join('c.theme', 't')
      ->where('t.id = :id')
      ->setParameter('id', $id)
      ->getQuery()
      ->getResult();
  }

  /**
   * Finds courses based on a set of filters.
   *
   * This method allows filtering courses by theme name, title search, price range, and badges.
   * It dynamically adjusts the query depending on which filters are provided.
   *
   * @param string|null $themeName The name of the theme to filter courses by (optional).
   * @param string|null $search A search term to filter course titles (optional).
   * @param string|null $priceRange A price range to filter courses by (optional).
   * @param array $badges An array of badge IDs to filter courses by (optional).
   *
   * @return Courses[] An array of Courses entities matching the provided filters.
   */
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
