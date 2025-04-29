<?php

namespace App\Service;

use App\Repository\OrderItemRepository;

/**
 * Service responsible for retrieving completed courses and lessons for a specific user.
 * 
 * This service interacts with the OrderItemRepository to fetch the completed courses and lessons
 * associated with a given user.
 */
class CompletedCoursesService
{
  private OrderItemRepository $orderItemRepository;

  /**
   * Constructor to initialize the service with the OrderItemRepository.
   * 
   * @param OrderItemRepository $orderItemRepository The repository for fetching order item data.
   */
  public function __construct(OrderItemRepository $orderItemRepository)
  {
    $this->orderItemRepository = $orderItemRepository;
  }

  /**
   * Retrieves the completed courses and lessons for a specific user.
   * 
   * This method fetches the completed courses and lessons by the given user from the
   * `OrderItemRepository`. The returned array will contain the course and lesson details, 
   * or `null` if no such records exist.
   * 
   * @param int $userId The ID of the user whose completed courses and lessons are to be fetched.
   * 
   * @return array An associative array containing the completed courses and lessons for the user.
   * The array contains:
   *  - 'course' => the completed course, or `null` if no course is completed.
   *  - 'lesson' => the completed lesson, or `null` if no lesson is completed.
   */
  public function getCompletedCourses(int $userId): array
  {
    // Retrieve completed courses and lessons from the repository
    $completedCourses = $this->orderItemRepository->findCompletedCoursesByUser($userId);

    // Return the results in an associative array
    return [
      'course' => $completedCourses['course'] ?? null,
      'lesson' => $completedCourses['lesson'] ?? null,
    ];
  }
}
