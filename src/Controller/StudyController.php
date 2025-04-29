<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\OrderItem;
use App\Security\Voter\UserVoter;
use App\Entity\Certifications;
use App\Entity\Completion;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Controller responsible for displaying the user's study progress, certifications, and order items.
 */
final class StudyController extends AbstractController
{
  /**
   * Displays the user's study progress, certifications, and associated order items.
   * 
   * This method retrieves the user's order items, certifications, and calculates the course completion progress
   * based on the user's course enrollments. It then renders the study page with this information.
   * 
   * @param int $id The ID of the user whose study data is to be retrieved.
   * @param EntityManagerInterface $em The Doctrine entity manager for interacting with the database.
   * 
   * @return Response The HTTP response containing the rendered study page.
   */
  #[Route('/study/{id}', name: 'app_study', methods: ['GET'])]
  public function getStudies(int $id, EntityManagerInterface $em): Response
  {
    try {
      // Retrieve the user by ID
      $user = $em->getRepository(User::class)->find($id);

      // Deny access unless the user has permission to edit the current user
      $this->denyAccessUnlessGranted(UserVoter::EDIT, $user);

      // Retrieve the order items associated with the user
      $getOrdersItems = $em->getRepository(OrderItem::class)->findOrderItemsByUser($id);
      // Retrieve the certifications associated with the user
      $getCertifications = $em->getRepository(Certifications::class)->findBy(['user' => $user->getId()]);

      // Repository for retrieving course completion data
      $completionRepo = $em->getRepository(Completion::class);

      // Array to store progress by course
      $progressByCourse = [];

      // Loop through the order items and calculate progress for each associated course
      foreach ($getOrdersItems as $orderItem) {
        $course = $orderItem->getCourse();

        if ($course) {
          // Calculate course completion percentage for the user
          $progressByCourse[$course->getId()] = $completionRepo->getCourseCompletionPercentage($user, $course);
        }
      }

      // Render the study page with the fetched data
      return $this->render('study/study.html.twig', [
        'ordersItems' => $getOrdersItems,
        'certifications' => $getCertifications,
        'progressByCourse' => $progressByCourse
      ]);
    } catch (\Exception $e) {
      // Handle any exceptions and show an error message
      $this->addFlash('error', $e->getMessage());

      // Redirect to the study page of the current logged-in user in case of error
      return $this->redirectToRoute('app_study', ['id' => $this->getUser()->getId()]);
    }
  }
}
