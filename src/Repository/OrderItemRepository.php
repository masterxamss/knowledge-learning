<?php

namespace App\Repository;

use App\Entity\OrderItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository for interacting with the OrderItem entity.
 *
 * This repository class provides methods for querying order items by user, 
 * retrieving most sold courses and lessons, and determining course completion status 
 * based on user purchases.
 *
 * @extends ServiceEntityRepository<OrderItem>
 */
class OrderItemRepository extends ServiceEntityRepository
{
  /**
   * Constructor.
   *
   * Initializes the repository with the ManagerRegistry and the OrderItem entity class.
   *
   * @param ManagerRegistry $registry The manager registry.
   */
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, OrderItem::class);
  }

  /**
   * Finds order items associated with a specific user.
   *
   * This method retrieves all order items where the associated order belongs to the specified user.
   *
   * @param int $id The ID of the user to filter order items by.
   *
   * @return OrderItem[] An array of OrderItem entities related to the specified user.
   */
  public function findOrderItemsByUser(int $id): array
  {
    return $this->createQueryBuilder('oi')
      ->join('oi.orders', 'o')
      ->where('o.user = :id')
      ->setParameter('id', $id)
      ->getQuery()
      ->getResult();
  }

  /**
   * Finds order items associated with lessons from a specific course.
   *
   * This method retrieves all order items where the associated lesson belongs to the specified course.
   *
   * @param int $id The ID of the course to filter order items by.
   *
   * @return OrderItem[] An array of OrderItem entities related to the specified course.
   */
  public function findOrderByLessonCourse(int $id): array
  {
    return $this->createQueryBuilder('oi')
      ->join('oi.lesson', 'l')
      ->where('l.course = :id')
      ->setParameter('id', $id)
      ->getQuery()
      ->getResult();
  }

  /**
   * Retrieves the most sold courses.
   *
   * This method counts the number of times each course appears in order items and returns the top 3 most sold courses.
   *
   * @return array An array of the top 3 most sold courses with their titles and the total sales count.
   */
  public function getMostSaleCourse(): array
  {
    return $this->createQueryBuilder('o')
      ->select('c.title AS course_title, COUNT(o.course) AS total_course')
      ->join('o.course', 'c')
      ->groupBy('o.course')
      ->orderBy('total_course', 'DESC')
      ->setMaxResults(3)
      ->getQuery()
      ->getResult();
  }

  /**
   * Retrieves the most sold lessons.
   *
   * This method counts the number of times each lesson appears in order items and returns the top 3 most sold lessons.
   *
   * @return array An array of the top 3 most sold lessons with their titles and the total sales count.
   */
  public function getMostSaleLesson(): array
  {
    return $this->createQueryBuilder('o')
      ->select('l.title AS lesson_title, COUNT(o.lesson) AS total_lesson')
      ->join('o.lesson', 'l')
      ->groupBy('o.lesson')
      ->orderBy('total_lesson', 'DESC')
      ->setMaxResults(3)
      ->getQuery()
      ->getResult();
  }

  /**
   * Finds completed courses by user.
   *
   * This method determines which courses a user has fully completed based on their order items,
   * considering both individual lessons purchased and complete courses bought.
   *
   * @param int $userId The ID of the user to check for completed courses.
   *
   * @return array An array of completed courses and lessons for the specified user.
   */
  public function findCompletedCoursesByUser(int $userId): array
  {
    $entityManager = $this->getEntityManager();

    $completedItems = []; // Array to store the results

    // Query to count the lessons bought per course
    $query = $entityManager->createQuery("
            SELECT IDENTITY(l.course) AS course_id, COUNT(l.id) AS lessons_bought
            FROM App\Entity\OrderItem oi
            JOIN oi.lesson l
            JOIN oi.orders o
            WHERE o.user = :userId
            AND o.paymentStatus = 'success'  -- Only successful payments
            GROUP BY l.course
        ")->setParameter('userId', $userId);

    $userLessons = $query->getResult();

    // Check if the user bought all lessons or the complete course
    foreach ($userLessons as $lessonData) {
      $courseId = $lessonData['course_id'];
      $lessonsBought = $lessonData['lessons_bought'];

      // Fetch total lessons for the course
      $totalLessons = $entityManager->createQuery("
                SELECT COUNT(l.id)
                FROM App\Entity\Lessons l
                WHERE l.course = :courseId
            ")->setParameter('courseId', $courseId)
        ->getSingleScalarResult();

      // Check if the user bought all lessons
      $isComplete = false;
      if ($lessonsBought == $totalLessons) {
        $isComplete = true;  // Bought all lessons
      }

      // Check if the user bought the entire course (not buying individual lessons)
      if (!$isComplete) {
        $coursePurchase = $entityManager->createQuery("
                    SELECT COUNT(oi.id)
                    FROM App\Entity\OrderItem oi
                    JOIN oi.orders o
                    WHERE o.user = :userId
                    AND o.paymentStatus = 'success'  -- Only successful payments
                    AND oi.course = :courseId
                ")->setParameter('userId', $userId)
          ->setParameter('courseId', $courseId)
          ->getSingleScalarResult();

        if ($coursePurchase > 0) {
          $isComplete = true;  // Bought the entire course
        }
      }

      // If the user completed the course
      if ($isComplete) {
        $completedItems['course'][] = $courseId; // Add to courses list
      }
    }

    // Query for courses purchased directly (not through lessons)
    $courseQuery = $entityManager->createQuery("
            SELECT DISTINCT IDENTITY(oi.course) AS course_id
            FROM App\Entity\OrderItem oi
            JOIN oi.orders o
            WHERE o.user = :userId
            AND oi.lesson IS NULL  -- Ensure we are only checking courses (no lessons)
            AND o.paymentStatus = 'success'  -- Only successful payments
        ")->setParameter('userId', $userId);

    $userCourses = $courseQuery->getResult();

    // Add directly purchased courses to the completed list
    foreach ($userCourses as $course) {
      $completedItems['course'][] = $course['course_id']; // Add to courses list
    }

    // Query for lessons bought directly (not part of a course)
    $lessonQuery = $entityManager->createQuery("
            SELECT DISTINCT IDENTITY(oi.lesson) AS lesson_id
            FROM App\Entity\OrderItem oi
            JOIN oi.orders o
            WHERE o.user = :userId
            AND oi.lesson IS NOT NULL  -- Ensure we are only checking lessons
            AND o.paymentStatus = 'success'  -- Only successful payments
        ")->setParameter('userId', $userId);

    $userLessonsDirect = $lessonQuery->getResult();

    // Add directly purchased lessons to the completed lessons list
    foreach ($userLessonsDirect as $lesson) {
      $completedItems['lesson'][] = $lesson['lesson_id']; // Add to lessons list
    }

    return $completedItems; // Return the array with completed courses and lessons
  }
}
