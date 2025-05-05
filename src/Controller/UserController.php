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

/**
 * Controller for managing user profiles, avatar, and user-related data.
 */
final class UserController extends AbstractController
{
  /**
   * Displays and updates the user data (profile).
   *
   * This method retrieves the user based on the provided ID and checks if the user exists and is verified.
   * A form is presented for updating user data. Upon successful submission, the user's data is updated.
   * 
   * @param int $id The ID of the user whose data is to be updated.
   * @param Request $request The HTTP request object.
   * @param EntityManagerInterface $entityManager The Doctrine entity manager for interacting with the database.
   * 
   * @return Response The rendered view of the user profile form.
   */
  #[Route('/user', name: 'app_user_data', methods: ['GET', 'POST'])]
  public function user(Request $request, EntityManagerInterface $entityManager): Response
  {

    $user = $this->getUser();

    try {

      $this->denyAccessUnlessGranted(UserVoter::EDIT, $user);

      // Check if user is verified
      if (!$user->getIsVerified()) {
        $this->addFlash('error', 'Your account is not yet activated');
        return $this->redirectToRoute('app_home');
      }

      // Prepare form
      $form = $this->createForm(UserDataType::class, $user);
      $form->handleRequest($request);

      // Update user data if the form is submitted and valid
      if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->flush();
        $this->addFlash('success', 'Information updated successfully');
      }

      return $this->render('user/user.html.twig', [
        'form' => $form->createView(),
        'title' => 'Profile',
        'subtitle' => 'Add information about yourself'
      ]);
    } catch (\Exception $e) {
      $this->addFlash('error', 'An error occurred' . $e->getMessage());
      return $this->redirectToRoute('app_home');
    }
  }

  /**
   * Allows the user to upload and update their avatar.
   *
   * This method handles the uploading and updating of the user's avatar image.
   * The form is used to submit the image, and if the form is valid, the image is uploaded,
   * and the user's avatar is updated.
   * 
   * @param int $id The ID of the user whose avatar is to be updated.
   * @param EntityManagerInterface $entityManager The Doctrine entity manager for interacting with the database.
   * @param Request $request The HTTP request object.
   * 
   * @return Response The rendered view of the avatar update form.
   */
  #[Route('/user/{id}/avatar', name: 'app_user_avatar')]
  public function userAvatar(int $id, EntityManagerInterface $entityManager, Request $request): Response
  {
    try {
      // Find user
      $user = $entityManager->getRepository(User::class)->find($id);

      // Check if user exists
      if (!$user) {
        return $this->redirectToRoute('app_user_avatar', ['id' => $this->getUser()->getId()]);
      }

      $this->denyAccessUnlessGranted(UserVoter::EDIT, $user);

      // Prepare form
      $form = $this->createForm(UserAvatarType::class, $user);
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
        try {
          // Update image
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
        'subtitle' => 'Add a photo to your profile',
      ]);
    } catch (\Exception $e) {
      $this->addFlash('error', 'An error occurred');
      return $this->redirectToRoute('app_user_avatar', ['id' => $this->getUser()->getId()]);
    }
  }

  /**
   * Displays the public profile of the user.
   *
   * This method renders the public profile page for a user. It assumes that the user has already
   * provided their profile information.
   * 
   * @param User $user The user whose public profile is being displayed.
   * 
   * @return Response The rendered view of the public profile page.
   */
  #[Route('/user/profil', name: 'app_user_profile')]
  public function userProfil(): Response
  {

    $user = $this->getUser();

    $this->denyAccessUnlessGranted(UserVoter::EDIT, $user);
    // HACK: Make user public profile
    return $this->render('user/user.html.twig', [
      'title' => 'Public Profile',
      'subtitle' => 'Preview of your profile'
    ]);
  }

  /**
   * Handles the avatar image upload for the user.
   * 
   * This function moves the uploaded image to the upload directory and updates the user's image field
   * with the new filename. If the user already has an image, the old image will be deleted.
   * 
   * @param FormInterface $form The form containing the image data.
   * @param User $user The user to update.
   * @param string $oldImage The filename of the old image, if any.
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

      // Delete the old image if it exists
      if ($oldImage) {
        $oldImagePath = $uploadDirectory . '/' . $oldImage;
        if (file_exists($oldImagePath)) {
          unlink($oldImagePath);
        }
      }

      try {
        // Move the uploaded image to the server's directory
        $imageFile->move($uploadDirectory, $newFilename);
        $user->setImage($newFilename);
      } catch (FileException $e) {
        throw new \Exception('Error while uploading the image.');
      }
    }
  }
}
