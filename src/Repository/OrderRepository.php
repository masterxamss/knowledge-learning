<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for interacting with the Order entity.
 *
 * This repository class provides methods for querying total sales, sales per client, 
 * and monthly sales, allowing for detailed sales reporting and analysis.
 *
 * @extends ServiceEntityRepository<Order>
 */
class OrderRepository extends ServiceEntityRepository
{
  /**
   * Constructor.
   *
   * Initializes the repository with the ManagerRegistry and the Order entity class.
   *
   * @param ManagerRegistry $registry The manager registry.
   */
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Order::class);
  }

  /**
   * Retrieves total sales for each month.
   *
   * This method queries the total sales (`total_price`) for each month, ordered in ascending order of months.
   * The results are returned as an array of month and total sales.
   *
   * @return array An array of month and total sales.
   */
  public function getTotalMonthlySells(): array
  {
    $conn = $this->getEntityManager()->getConnection();

    $sql = '
            SELECT DATE_FORMAT(created_at, "%Y-%m") AS month, SUM(total_price) AS total
            FROM `order`
            GROUP BY month
            ORDER BY month ASC
        ';

    $stmt = $conn->prepare($sql);
    return $stmt->executeQuery()->fetchAllAssociative();
  }

  /**
   * Retrieves the total sales for the top 3 clients.
   *
   * This method calculates the total sales (`totalPrice`) for each client, grouped by user,
   * and returns the top 3 clients with the highest total sales.
   *
   * @return array An array of the top 3 clients with their email and total sales.
   */
  public function getTotalSellsPerClient(): array
  {
    return $this->createQueryBuilder('o')
      ->select('u.email AS client_email, SUM(o.totalPrice) AS total')
      ->join('o.user', 'u')
      ->groupBy('o.user')
      ->orderBy('total', 'DESC')
      ->setMaxResults(3)
      ->getQuery()
      ->getResult();
  }

  /**
   * Retrieves total sales for the current year.
   *
   * This method calculates the total sales (`totalPrice`) for the current year based on the created date.
   *
   * @return float|null The total sales for the current year, or null if no sales found.
   */
  public function getTotalSalesInCurrentYear(): ?float
  {
    $startOfYear = new \DateTime(date('Y-01-01 00:00:00'));
    $endOfYear = new \DateTime(date('Y-12-31 23:59:59'));

    return $this->createQueryBuilder('o')
      ->select('SUM(o.totalPrice) as total_sales')
      ->where('o.createdAt BETWEEN :start AND :end')
      ->setParameter('start', $startOfYear)
      ->setParameter('end', $endOfYear)
      ->getQuery()
      ->getSingleScalarResult();
  }
}
