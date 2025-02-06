<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function index(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);


        return $this->render('register/register.html.twig', [
            'controller_name' => 'RegisterController',
            'form' => $form
        ]);
    }
}
