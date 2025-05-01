<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Certifications;
use App\Security\Voter\UserVoter;

/**
 * Controller for managing user certifications.
 *
 * This controller handles the logic for displaying all certifications 
 * for the authenticated user, as well as showing details for a specific 
 * certification. It ensures that the user has the appropriate access rights 
 * using a custom voter (`UserVoter::EDIT`).
 *
 */
final class CertificationsController extends AbstractController
{
  /**
   * Displays all certifications for the authenticated user.
   *
   * This method retrieves all certifications linked to the current authenticated user
   * and displays them in a list view. If the user has no certifications, a flash message
   * is shown. Access is granted only if the user has the appropriate permissions.
   *
   * @param EntityManagerInterface $em The Doctrine EntityManager for interacting with the database.
   *
   * @return Response The response containing the rendered view of the certifications.
   */
  #[Route('/certifications/', name: 'app_certifications', methods: ['GET'])]
  public function getAllCertifications(EntityManagerInterface $em): Response
  {
    try {
      // Deny access unless the user has the EDIT permission
      $this->denyAccessUnlessGranted(UserVoter::EDIT, $this->getUser());

      // Retrieve certifications for the authenticated user
      $certifications = $em->getRepository(Certifications::class)->findBy(['user' => $this->getUser()]);

      // If no certifications are found, display an info message
      if (!$certifications) {
        $this->addFlash('info', 'You have no certifications.');
      }

      // Render and return the certifications view
      return $this->render('certifications/certifications.html.twig', [
        'certifications' => $certifications
      ]);
    } catch (\Exception $e) {
      // Handle any errors and redirect the user
      $this->addFlash('error', $e->getMessage());
      return $this->redirectToRoute('app_home');
    }
  }

  /**
   * Displays the details of a specific certification.
   *
   * This method retrieves the certification specified by its ID for the
   * authenticated user and displays its details. If the certification cannot 
   * be found or there is an error, the user is redirected to the certifications 
   * list. Access is restricted to users with the appropriate permissions.
   *
   * @param int $id The ID of the certification to display.
   * @param EntityManagerInterface $em The Doctrine EntityManager for interacting with the database.
   *
   * @return Response The response containing the rendered view of the certification details.
   */
  #[Route('/certification/{id}', name: 'app_certification', requirements: ['id' => '\d+'], methods: ['GET'])]
  public function getCertification(int $id, EntityManagerInterface $em): Response
  {
    try {
      // Deny access unless the user has the EDIT permission
      $this->denyAccessUnlessGranted(UserVoter::EDIT, $this->getUser());

      // Retrieve the specific certification for the authenticated user
      $certification = $em->getRepository(Certifications::class)->findBy(['id' => $id, 'user' => $this->getUser()]);

      // Render and return the certification details view
      return $this->render('certifications/certification.html.twig', [
        'certification' => $certification
      ]);
    } catch (\Exception $e) {
      // Handle any errors and redirect the user
      $this->addFlash('error', $e->getMessage());
      return $this->redirectToRoute('app_certifications');
    }
  }
}
