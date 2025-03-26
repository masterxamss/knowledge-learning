<?php

namespace App\Controller;

use App\Entity\Courses;
use App\Entity\Lessons;
use App\Entity\User;
use App\Entity\Cart;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class CartController extends AbstractController
{
  #[Route('/cart', name: 'app_cart', methods: ['GET'])]
  public function getCart(EntityManagerInterface $em): Response
  {
    $total = 0;

    $getCart = $em->getRepository(Cart::class)->findBy(['user' => $this->getUser()]);

    if (!$getCart) {
      $this->addFlash('info', 'Votre panier est vide');
    }

    if ($getCart) {
      foreach ($getCart as $cart) {
        $total += $cart->getPrice();
      }
    }

    return $this->render('cart/cart.html.twig', [
      'cart' => $getCart,
      'total' => $total,
    ]);
  }


  #[Route('/cart/add-item', name: 'app_cart_add', methods: ['POST'])]
  public function addCartItem(Request $request, EntityManagerInterface $em): Response
  {
    try {
      // Validate the CSRF token
      $submittedToken = $request->request->get('token');
      if (!$this->isCsrfTokenValid('cart-add', $submittedToken)) {
        $this->addFlash('error', 'Invalid CSRF token');
        return $this->redirectToRoute('app_cart');
      }

      // Get user from the request and search 
      $userId = $request->request->get('user_id');
      $user = $em->getRepository(User::class)->find($userId);
      if (!$user) {
        $this->addFlash('info', 'Vous devez vous connecter pour effectuer cette action');
        return $this->redirectToRoute('app_home');
      }

      // Get the lesson or course from the request
      $lessonId = $request->request->get('lesson_id');
      $courseId = $request->request->get('course_id');

      // Create the cart item
      $cart = new Cart();

      if ($lessonId) {

        // Check if the lesson is already in the cart
        $cartLesson = $em->getRepository(Cart::class)->findBy(['lesson' => $lessonId]);
        if (!empty($cartLesson)) {
          return $this->redirectToRoute('app_cart');
        }

        // Search for the lesson
        $lesson = $em->getRepository(Lessons::class)->find($lessonId);
        $cart->setLesson($lesson);
        $cart->setPrice($lesson->getPrice());
      } elseif ($courseId) {

        // Check if the course is already in the cart
        $cartCourse = $em->getRepository(Cart::class)->findBy(['course' => $courseId]);
        if (!empty($cartCourse)) {
          return $this->redirectToRoute('app_cart');
        }

        $course = $em->getRepository(Courses::class)->find($courseId);
        $cart->setCourse($course);
        $cart->setPrice($course->getPrice());
      }

      // Set the user
      $cart->setUser($user);

      // Save the cart item
      $em->persist($cart);
      $em->flush();

      return $this->redirectToRoute('app_cart');
    } catch (\Exception $e) {
      $this->addFlash('error', 'Une erreur est survenue lors de l\'ajout au panier.' . $e->getMessage());
      return $this->redirectToRoute('app_cart');
    }
  }

  #[Route('/cart/delete-item', name: 'app_cart_delete', methods: ['POST'])]
  public function deleteCartItem(Request $request, EntityManagerInterface $em): Response
  {
    try {
      // Validate the CSRF token
      $submittedToken = $request->request->get('token');
      if (!$this->isCsrfTokenValid('cart-delete', $submittedToken)) {
        $this->addFlash('error', 'Invalid CSRF token');
        return $this->redirectToRoute('app_cart');
      }

      // Get the cart from the request
      $cartId = $request->request->get('id');
      $cart = $em->getRepository(Cart::class)->find($cartId);

      // Delete the cart
      if ($cart) {
        $em->remove($cart);
        $em->flush();
      } else {
        $this->addFlash('error', 'Item introuvable');
      }
    } catch (\Exception $e) {
      $this->addFlash('error', 'Une erreur est survenue lors de la suppression.' . $e->getMessage());
    }

    return $this->redirectToRoute('app_cart');
  }
}
