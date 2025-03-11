<?php

namespace App\Controller;

use App\Entity\Themes;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;

class HomeController extends AbstractController
{
  #[Route('/', name: 'app_home')]
  public function index(EntityManagerInterface $em): Response
  {
    $themes = $em->getRepository(Themes::class)->findAll();

    if (!$themes) {
      $this->addFlash('error', 'Aucun thème disponible');
      return $this->redirectToRoute('app_home');
    }

    return $this->render('home/home.html.twig', [
      'themes' => $themes
    ]);
  }
}
