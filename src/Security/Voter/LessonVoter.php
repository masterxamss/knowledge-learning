<?php

namespace App\Security\Voter;


use App\Entity\Lessons;
use App\Entity\Courses;
use App\Repository\OrderItemRepository;
use App\Repository\OrderRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class LessonVoter extends Voter
{
  public const VIEW = 'VIEW';

  private OrderItemRepository $orderItemRepository;
  private OrderRepository $orderRepository;

  public function __construct(OrderItemRepository $orderItemRepository, OrderRepository $orderRepository)
  {
    $this->orderItemRepository = $orderItemRepository;
    $this->orderRepository = $orderRepository;
  }

  protected function supports(string $attribute, mixed $subject): bool
  {
    // Verify if the attribute is 'VIEW' and if the subject is an array containing 'lesson' and 'course'
    if ($attribute === self::VIEW && is_array($subject)) {
      return isset($subject['lesson']) && $subject['lesson'] instanceof Lessons
        && isset($subject['course']) && $subject['course'] instanceof Courses;
    }
    return false;
  }
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
