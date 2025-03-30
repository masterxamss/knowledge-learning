<?php

namespace App\Repository;

use App\Entity\OrderItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OrderItem>
 */
class OrderItemRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, OrderItem::class);
  }

  public function findOrderItemsByUser($id)
  {
    return $this->createQueryBuilder('oi')
      ->join('oi.orders', 'o')
      ->where('o.user = :id')
      ->setParameter('id', $id)
      ->getQuery()
      ->getResult();
  }

  public function findOrderByLessonCourse($id)
  {
    return $this->createQueryBuilder('oi')
      ->join('oi.lesson', 'l')
      ->where('l.course = :id')
      ->setParameter('id', $id)
      ->getQuery()
      ->getResult();
  }

  /**
   * Get the IDs of the completed courses by the user through the purchase of all lessons.
   */
  /*public function findCompletedCoursesByUser(int $userId): array
  {
    $entityManager = $this->getEntityManager();

    $query = $entityManager->createQuery("
      SELECT IDENTITY(l.course) AS course_id, COUNT(l.id) AS lessons_bought
      FROM App\Entity\OrderItem oi
      JOIN oi.lesson l
      JOIN oi.orders o
      WHERE o.user = :userId
      GROUP BY l.course
    ")->setParameter('userId', $userId);

    $userLessons = $query->getResult();

    $completedCourses = [];

    foreach ($userLessons as $lessonData) {
      $courseId = $lessonData['course_id'];
      $lessonsBought = $lessonData['lessons_bought'];

      // Search the total number of lessons of the course
      $totalLessons = $entityManager->createQuery("
                SELECT COUNT(l.id)
                FROM App\Entity\Lessons l
                WHERE l.course = :courseId
            ")->setParameter('courseId', $courseId)
        ->getSingleScalarResult();

      // If the user bought all the lessons, add the course to the completed list
      if ($lessonsBought == $totalLessons) {
        $completedCourses[] = $courseId;
      }
    }

    return $completedCourses;
  }*/

  public function findCompletedCoursesByUser(int $userId): array
  {
    $entityManager = $this->getEntityManager();

    $completedItems = []; // Array para armazenar os resultados

    // Query para contar as lições compradas por cada curso
    $query = $entityManager->createQuery("
        SELECT IDENTITY(l.course) AS course_id, COUNT(l.id) AS lessons_bought
        FROM App\Entity\OrderItem oi
        JOIN oi.lesson l
        JOIN oi.orders o
        WHERE o.user = :userId
        AND o.paymentStatus = 'success'  -- Apenas ordens com pagamento bem-sucedido
        GROUP BY l.course
    ")->setParameter('userId', $userId);

    $userLessons = $query->getResult();

    // Verificar se o utilizador comprou todas as lições ou o curso completo
    foreach ($userLessons as $lessonData) {
      $courseId = $lessonData['course_id'];
      $lessonsBought = $lessonData['lessons_bought'];

      // Buscar o número total de lições do curso
      $totalLessons = $entityManager->createQuery("
            SELECT COUNT(l.id)
            FROM App\Entity\Lessons l
            WHERE l.course = :courseId
        ")->setParameter('courseId', $courseId)
        ->getSingleScalarResult();

      // Verificar se o utilizador comprou todas as lições do curso
      $isComplete = false;
      if ($lessonsBought == $totalLessons) {
        $isComplete = true;  // Comprou todas as lições
      }

      // Verificar se o utilizador comprou o curso completo de uma vez (sem comprar lições separadas)
      if (!$isComplete) {
        $coursePurchase = $entityManager->createQuery("
                SELECT COUNT(oi.id)
                FROM App\Entity\OrderItem oi
                JOIN oi.orders o
                WHERE o.user = :userId
                AND o.paymentStatus = 'success'  -- Apenas ordens com pagamento bem-sucedido
                AND oi.course = :courseId
            ")->setParameter('userId', $userId)
          ->setParameter('courseId', $courseId)
          ->getSingleScalarResult();

        if ($coursePurchase > 0) {
          $isComplete = true;  // Comprou o curso completo de uma vez
        }
      }

      // Se o utilizador completou o curso (comprou todas as lições ou comprou o curso completo)
      if ($isComplete) {
        $completedItems['course'][] = $courseId; // Adicionar ao array de cursos
      }
    }

    // Query para cursos comprados diretamente (não através de lições)
    $courseQuery = $entityManager->createQuery("
        SELECT DISTINCT IDENTITY(oi.course) AS course_id
        FROM App\Entity\OrderItem oi
        JOIN oi.orders o
        WHERE o.user = :userId
        AND oi.lesson IS NULL  -- Certificar que estamos a verificar apenas os cursos (sem lição)
        AND o.paymentStatus = 'success'  -- Apenas ordens com pagamento bem-sucedido
    ")->setParameter('userId', $userId);

    $userCourses = $courseQuery->getResult();

    // Adicionar os cursos comprados diretamente à lista de cursos completados
    foreach ($userCourses as $course) {
      $completedItems['course'][] = $course['course_id']; // Adicionar ao array de cursos
    }

    // Query para lições compradas (sem curso completo)
    $lessonQuery = $entityManager->createQuery("
        SELECT DISTINCT IDENTITY(oi.lesson) AS lesson_id
        FROM App\Entity\OrderItem oi
        JOIN oi.orders o
        WHERE o.user = :userId
        AND oi.lesson IS NOT NULL  -- Certificar que estamos a verificar apenas as lições
        AND o.paymentStatus = 'success'  -- Apenas ordens com pagamento bem-sucedido
    ")->setParameter('userId', $userId);

    $userLessonsDirect = $lessonQuery->getResult();

    // Adicionar as lições compradas diretamente à lista de lições completadas
    foreach ($userLessonsDirect as $lesson) {
      $completedItems['lesson'][] = $lesson['lesson_id']; // Adicionar ao array de lições
    }
    return $completedItems; // Retornar o array com cursos e lições
  }
}
