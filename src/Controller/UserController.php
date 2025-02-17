<?php

namespace App\Controller;

use App\Form\UserAvatarType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

final class UserController extends AbstractController
{
  #[Route('/user/{id}', name: 'app_user')]
  public function user(): Response
  {
    return $this->render('user/user.html.twig', [
      'path' => 'user',
      'title' => 'Profil public',
      'subtitle' => 'Ajouter des informations sur vous-même'
    ]);
  }

  #[Route('/user/{id}/avatar', name: 'app_user_avatar')]
  public function userAvatar(int $id, EntityManagerInterface $entityManager, Request $request): Response
  {

    // Find user
    $user = $entityManager->getRepository(User::class)->find($id);
    if (!$user) {
      $this->addFlash('alert-danger', 'User not found');
    }


    // Prepare form
    $form = $this->createForm(UserAvatarType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      try {
        //Update image
        $this->handleImageUpload($form, $user, $user->getImage());


        $entityManager->flush();

        $this->addFlash('alert-success', 'Avatar updated successfully');
      } catch (\Exception $e) {
        $this->addFlash('alert-danger', $e->getMessage());
      }

      return $this->redirectToRoute('app_user_avatar', ['id' => $user->getId()]);
    }

    return $this->render('user/user.html.twig', [
      'path' => 'userAvatar',
      'form' => $form->createView(),
      'title' => 'Photo',
      'subtitle' => 'Ajouter une photo à votre profil',
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
