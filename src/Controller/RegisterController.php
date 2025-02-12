<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use App\Service\MailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Uid\Uuid;

class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function index(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher,
        MailService $mailService
    ): Response {

        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // check if email already exists
            $existingUser = $em->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);

            // if email already exists show error
            if ($existingUser) {
                $this->addFlash('error', 'Impossible de créer l\'utilisateur');
                return $this->redirectToRoute('app_register');
            }

            // set the roles
            $user->setRoles(['ROLE_USER']);

            // hash the password
            $user->setPassword($hasher->hashPassword($user, $user->getPassword()));

            // set is verified to false
            $user->setIsVerified(false);

            // set is active to false
            $user->setActive(false);

            // set created at and updated at
            $user->setCreatedAt(new \DateTimeImmutable());
            $user->setUpdatedAt(new \DateTimeImmutable());

            // generate activation token and set it
            $activationToken = Uuid::v4();
            $user->setActivationToken($activationToken);

            try {
                // save the user
                $em->persist($user);
                $em->flush();

                //send activation email
                $mailService->sendActivationEmail($user->getEmail(), $user->getActivationToken());

                $this->addFlash('success', 'Enregistrement réussi. Vérifiez votre e-mail pour activer votre compte 🎉');

                return $this->redirectToRoute('app_register');
            } catch (\Exception $e) {
                // log the error
                $this->addFlash('error', 'Une erreur est survenue lors de l\'enregistrement de l\'utilisateur');
                return $this->redirectToRoute('app_register');
            }
        }

        // render the register form
        return $this->render('register/register.html.twig', [
            'controller_name' => 'RegisterController',
            'form' => $form
        ]);
    }
}
