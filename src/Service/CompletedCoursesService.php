<?php

namespace App\Service;

use App\Repository\OrderItemRepository;

class CompletedCoursesService
{
  private OrderItemRepository $orderItemRepository;

  public function __construct(OrderItemRepository $orderItemRepository)
  {
    $this->orderItemRepository = $orderItemRepository;
  }

  public function getCompletedCourses(int $userId): array
  {
    $completedCourses = $this->orderItemRepository->findCompletedCoursesByUser($userId);

    return [
      'course' => $completedCourses['course'] ?? null,
      'lesson' => $completedCourses['lesson'] ?? null,
    ];
  }
}
