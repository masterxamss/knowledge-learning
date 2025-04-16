<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Certifications;

final class CertificationsController extends AbstractController
{
  #[Route('/certifications', name: 'app_certifications')]
  public function getAllCertifications(EntityManagerInterface $em): Response
  {
    $certifications = $em->getRepository(Certifications::class)->findBy(['user' => $this->getUser()]);

    return $this->render('certifications/certifications.html.twig', [
      'certifications' => $certifications
    ]);
  }
}
