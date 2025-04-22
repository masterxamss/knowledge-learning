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

final class StudyController extends AbstractController
{
  #[Route('/study/{id}', name: 'app_study', methods: ['GET'])]
  public function getStudies(int $id, EntityManagerInterface $em): Response
  {
    try {
      $user = $em->getRepository(User::class)->find($id);

      $this->denyAccessUnlessGranted(UserVoter::EDIT, $user);

      $getOrdersItems = $em->getRepository(OrderItem::class)->findOrderItemsByUser($id);
      $getCertifications = $em->getRepository(Certifications::class)->findBy(['user' => $user->getId()]);

      $completionRepo = $em->getRepository(Completion::class);

      $progressByCourse = [];

      foreach ($getOrdersItems as $orderItem) {
        $course = $orderItem->getCourse();

        if ($course) {
          $progressByCourse[$course->getId()] = $completionRepo->getCourseCompletionPercentage($user, $course);
        }
      }
      return $this->render('study/study.html.twig', [
        'ordersItems' => $getOrdersItems,
        'certifications' => $getCertifications,
        'progressByCourse' => $progressByCourse
      ]);
    } catch (\Exception $e) {
      $this->addFlash('error', $e->getMessage());
      return $this->redirectToRoute('app_study', ['id' => $this->getUser()->getId()]);
    }
  }
}
