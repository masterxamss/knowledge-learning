<?php

namespace App\Controller;

use App\Form\UserAvatarType;
use App\Form\UserDataType;
use App\Entity\User;
use App\Security\Voter\UserVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class UserController extends AbstractController
{
  #[Route('/user/{id}', name: 'app_user_data', methods: ['GET', 'POST'])]
  #[IsGranted(UserVoter::EDIT, subject: 'user')]
  public function user(int $id, EntityManagerInterface $entityManager, Request $request, User $user): Response
  {
    try {
      // Find user
      $user = $entityManager->getRepository(User::class)->find($id);

      // Check if user exists
      if (!$user) {
        $this->addFlash('error', 'Utilisateur introuvable');
        return $this->redirectToRoute('app_home');
      }

      // Check if user is verified
      if (!$user->getIsVerified()) {
        $this->addFlash('error', 'Votre compte n\'est pas encore activé');
        return $this->redirectToRoute('app_home');
      }

      // Prepare form
      $form = $this->createForm(UserDataType::class, $user);
      $form->handleRequest($request);

      // Update user
      if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();
        $this->addFlash('success', 'Informations mises à jour');
      }
    } catch (\Exception $e) {
      $this->addFlash('error', 'Une erreur est survenue' . $e->getMessage());
      return $this->redirectToRoute('app_home');
    }

    return $this->render('user/user.html.twig', [
      'form' => $form->createView(),
      'title' => 'Profil',
      'subtitle' => 'Ajouter des informations sur vous-même'
    ]);
  }

  #[Route('/user/{id}/avatar', name: 'app_user_avatar')]
  #[IsGranted('ROLE_USER')]
  public function userAvatar(int $id, EntityManagerInterface $entityManager, Request $request): Response
  {

    // Find user
    $user = $entityManager->getRepository(User::class)->find($id);
    if (!$user) {
      $this->addFlash('error', 'User not found');
    }

    // Prepare form
    $form = $this->createForm(UserAvatarType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      try {
        //Update image
        $this->handleImageUpload($form, $user, $user->getImage());

        $entityManager->flush();

        $this->addFlash('success', 'Avatar updated successfully');
      } catch (\Exception $e) {
        $this->addFlash('error', $e->getMessage());
      }

      return $this->redirectToRoute('app_user_avatar', ['id' => $user->getId()]);
    }

    return $this->render('user/user.html.twig', [
      'form' => $form->createView(),
      'title' => 'Photo',
      'subtitle' => 'Ajouter une photo à votre profil',
    ]);
  }


  #[Route('/user/{id}/profil', name: 'app_user_profile')]
  #[IsGranted('ROLE_USER')]
  public function userProfil(): Response
  {
    return $this->render('user/user.html.twig', [
      'title' => 'Profile Public',
      'subtitle' => 'Aperçu de votre profil'
    ]);
  }

  /**
   * Handles the avatar image upload of the User.
   * 
   * This function will move the uploaded image to the upload directory and update
   * the user image field with the new filename. If the user already has
   * an image, it will also delete the old image.
   * 
   * @param FormInterface $form     The form containing the image data.
   * @param User          $user     The user to update.
   * @param string        $oldImage The filename of the old image, if any.
   * 
   * @throws \Exception If there is an error while uploading the image.
   * 
   * @return void
   */
  private function handleImageUpload($form, User $user, ?string $oldImage = null): void
  {
    $imageFile = $form->get('image')->getData();
    if ($imageFile) {
      $uploadDirectory = $this->getParameter('upload_directory');
      $newFilename = uniqid() . '.' . $imageFile->guessExtension();

      if ($oldImage) {
        $oldImagePath = $uploadDirectory . '/' . $oldImage;
        if (file_exists($oldImagePath)) {
          unlink($oldImagePath);
        }
      }

      try {
        $imageFile->move($uploadDirectory, $newFilename);
        $user->setImage($newFilename);
      } catch (FileException $e) {
        throw new \Exception('Erreur lors du téléchargement de l\'image.');
      }
    }
  }
}
