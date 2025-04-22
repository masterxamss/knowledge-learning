<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Certifications;
use App\Security\Voter\UserVoter;

final class CertificationsController extends AbstractController
{
  #[Route('/certifications/', name: 'app_certifications', methods: ['GET'])]
  public function getAllCertifications(EntityManagerInterface $em): Response
  {
    try {
      $this->denyAccessUnlessGranted(UserVoter::EDIT, $this->getUser());

      $certifications = $em->getRepository(Certifications::class)->findBy(['user' => $this->getUser()]);

      if (!$certifications) {
        $this->addFlash('info', 'Vous n\'avez aucune certification.');
      }

      return $this->render('certifications/certifications.html.twig', [
        'certifications' => $certifications
      ]);
    } catch (\Exception $e) {
      $this->addFlash('error', $e->getMessage());
      return $this->redirectToRoute('app_home');
    }
  }

  #[Route('/certification/{id}', name: 'app_certification', methods: ['GET'])]
  public function getCertification(int $id, EntityManagerInterface $em): Response
  {
    try {
      $this->denyAccessUnlessGranted(UserVoter::EDIT, $this->getUser());

      $certification = $em->getRepository(Certifications::class)->findBy(['id' => $id]);

      if (!$certification) {
        $this->addFlash('info', 'Vous n\'avez aucune certification.');
      }

      return $this->render('certifications/certification.html.twig', [
        'certification' => $certification
      ]);
    } catch (\Exception $e) {
      $this->addFlash('error', $e->getMessage());
      return $this->redirectToRoute('app_certifications');
    }
  }
}
