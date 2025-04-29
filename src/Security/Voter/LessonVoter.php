<?php

namespace App\Security\Voter;

use App\Entity\Lessons;
use App\Entity\Courses;
use App\Repository\OrderItemRepository;
use App\Repository\OrderRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Voter to authorize access to a lesson based on whether the user has purchased it or the course it belongs to.
 * 
 * This voter checks if a user has access to a specific lesson. The access is granted if the user has either
 * bought the lesson directly or purchased the course the lesson belongs to. 
 * 
 * The voter supports the 'VIEW' attribute.
 */
final class LessonVoter extends Voter
{
  public const VIEW = 'VIEW';

  private OrderItemRepository $orderItemRepository;
  private OrderRepository $orderRepository;

  /**
   * Constructor.
   *
   * Initializes the repositories for OrderItem and Order to check the user's purchases.
   *
   * @param OrderItemRepository $orderItemRepository The repository for order items.
   * @param OrderRepository $orderRepository The repository for orders.
   */
  public function __construct(OrderItemRepository $orderItemRepository, OrderRepository $orderRepository)
  {
    $this->orderItemRepository = $orderItemRepository;
    $this->orderRepository = $orderRepository;
  }

  /**
   * Checks if the attribute and subject are supported by this voter.
   *
   * This method checks whether the attribute is 'VIEW' and if the subject is an array containing 
   * both a lesson and a course.
   *
   * @param string $attribute The attribute being checked.
   * @param mixed $subject The subject being evaluated, expected to be an array with 'lesson' and 'course'.
   *
   * @return bool Returns true if the attribute is 'VIEW' and the subject contains both a lesson and a course.
   */
  protected function supports(string $attribute, mixed $subject): bool
  {
    // Verify if the attribute is 'VIEW' and if the subject is an array containing 'lesson' and 'course'
    if ($attribute === self::VIEW && is_array($subject)) {
      return isset($subject['lesson']) && $subject['lesson'] instanceof Lessons
        && isset($subject['course']) && $subject['course'] instanceof Courses;
    }
    return false;
  }

  /**
   * Performs the vote on the 'VIEW' attribute, checking if the user is authorized to view the lesson.
   *
   * This method checks whether the user has purchased the lesson directly or if they have bought the 
   * course that the lesson belongs to. If either condition is met, access is granted.
   *
   * @param string $attribute The attribute being checked, expected to be 'VIEW'.
   * @param mixed $subject The subject being evaluated, expected to be an array with 'lesson' and 'course'.
   * @param TokenInterface $token The authentication token of the user making the request.
   *
   * @return bool Returns true if the user has access, false otherwise.
   */
  protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
  {
    $user = $token->getUser();

    // If the user is anonymous, access is denied
    if (!$user instanceof UserInterface) {
      return false;
    }

    // Verify if the subject is an array containing 'lesson' and 'course'
    if (is_array($subject) && isset($subject['lesson']) && $subject['lesson'] instanceof Lessons) {
      $lesson = $subject['lesson'];
      $course = $subject['course'];

      // Verify if the user has bought the lesson or the course
      $order = $this->orderRepository->findBy([
        'user' => $user,
        'paymentStatus' => 'success'
      ]);
      if (!$order) {
        return false;
      }

      // Verify if the user has bought the lesson
      $orderItem = $this->orderItemRepository->findOneBy([
        'lesson' => $lesson,
        'orders' => $order
      ]);

      if ($orderItem) {
        return true;
      }

      // Verify if the user has bought the course
      $orderItemCourse = $this->orderItemRepository->findOneBy([
        'course' => $course,
        'orders' => $order
      ]);

      if ($orderItemCourse) {
        return true;
      }
    }

    return false;
  }
}
