<?php

namespace App\Controller;

use App\Entity\Courses;
use App\Entity\Lessons;
use App\Entity\Cart;
use App\Entity\OrderItem;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Controller for managing the shopping cart.
 *
 * This controller handles the logic for displaying the user's cart,
 * adding items (lessons and courses) to the cart, and deleting items from the cart.
 * It ensures that users are authenticated, their email is verified, and prevents 
 * duplicate items from being added to the cart.
 * It also calculates the total, discount, and TVA for each cart item.
 *
 */
#[IsGranted('ROLE_USER')]
final class CartController extends AbstractController
{
  /**
   * Displays the user's cart.
   *
   * @param EntityManagerInterface $em The Entity Manager instance
   *
   * @return Response The rendered cart page
   */
  #[Route('/cart', name: 'app_cart', methods: ['GET'])]
  public function getCart(EntityManagerInterface $em): Response
  {
    $total = 0;
    $subTotal = 0;
    $totalDiscount = 0;
    $totalTva = 0;

    // Fetch the user's cart items from the database
    $getCart = $em->getRepository(Cart::class)->findBy(['user' => $this->getUser()]);

    if (!$getCart) {
      $this->addFlash('info', 'Votre panier est vide');
    }

    // Loop through the cart items and calculate totals
    foreach ($getCart as $cart) {
      $totalTva += $cart->getTva();
      $totalDiscount += $cart->getDiscount();
      $subTotal += $cart->getSubTotal();
      $total += $cart->getTotal();
    }

    // Render the cart page with the calculated totals
    return $this->render('cart/cart.html.twig', [
      'cart' => $getCart,
      'subTotal' => number_format($subTotal, 2, '.', ''),
      'totalDiscount' => number_format($totalDiscount, 2, '.', ''),
      'totalTva' => number_format($totalTva, 2, '.', ''),
      'total' => number_format($total, 2, '.', ''),
    ]);
  }

  /**
   * Adds an item to the user's cart.
   *
   * @param Request $request The request object
   * @param EntityManagerInterface $em The Entity Manager instance
   *
   * @return Response Redirects to the cart page
   */
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

      // Get the authenticated user
      $user = $this->getUser();
      if (!$user) {
        $this->addFlash('info', 'You need to log in to perform this action');
        return $this->redirectToRoute('app_home');
      }

      // Verify if the user is verified
      if (!$user->getIsVerified()) {
        $this->addFlash('info', 'Please verify your email to make a purchase');
        return $this->redirectToRoute('app_home');
      }

      // Get lesson and course IDs from the request
      $lessonId = $request->request->get('lesson_id');
      $courseId = $request->request->get('course_id');

      // Create the cart item
      $cart = new Cart();
      $cart->setUser($user);

      // Handle lesson addition to the cart
      if ($lessonId) {
        // Check if the lesson is already in the cart
        if ($em->getRepository(Cart::class)->findOneBy(['lesson' => $lessonId, 'user' => $user])) {
          return $this->redirectToRoute('app_cart');
        }

        $lesson = $em->getRepository(Lessons::class)->find($lessonId);
        if (!$lesson) {
          $this->addFlash('error', 'Lesson not found');
          return $this->redirectToRoute('app_cart');
        }

        // Prevent adding a lesson if the entire course is already in the cart
        if ($em->getRepository(Cart::class)->findOneBy(['course' => $lesson->getCourse(), 'user' => $user])) {
          $this->addFlash('info', 'Ce cours contient une leçon en panier');
          return $this->redirectToRoute('app_cart');
        }

        $cart->setLesson($lesson);
        $subTotal = $lesson->getPrice();
        $cart->setPrice($subTotal);
        $cart->setSubTotal($subTotal);
        $totalTva = $subTotal * 0.20;
        $cart->setTva($totalTva);
        $cart->setTotal($subTotal + $totalTva);
      }

      // Handle course addition to the cart
      if ($courseId) {
        // Check if the course is already in the cart
        if ($em->getRepository(Cart::class)->findOneBy(['course' => $courseId, 'user' => $user])) {
          return $this->redirectToRoute('app_cart');
        }

        $course = $em->getRepository(Courses::class)->find($courseId);
        if (!$course) {
          $this->addFlash('error', 'Cours non trouvé');
          return $this->redirectToRoute('app_cart');
        }

        // Remove lessons from the cart if they belong to the course
        $cartLessons = $em->getRepository(Cart::class)->findBy(['user' => $user]);
        $lessonsBought = 0;
        foreach ($cartLessons as $item) {
          if ($item->getLesson() && $item->getLesson()->getCourse()->getId() === $course->getId()) {
            $em->remove($item);
          }
        }

        // Check for already purchased lessons in the course
        $boughtLessons = $em->getRepository(OrderItem::class)->findOrderByLessonCourse($course->getId());
        foreach ($boughtLessons as $lesson) {
          $lessonsBought += $lesson->getLesson()->getPrice();
        }

        // Apply discount if needed
        $cart->setPrice($course->getPrice());
        $cart->setDiscount($lessonsBought);
        $subTotal = $course->getPrice() - $lessonsBought;
        $cart->setSubTotal(max($subTotal, 0)); // Ensure it doesn't go negative

        // Calculate TVA and final price
        $totalTva = $subTotal * 0.20;
        $cart->setTva($totalTva);
        $cart->setTotal($subTotal + $totalTva);

        $cart->setCourse($course);
      }

      // Persist the cart item in the database
      $em->persist($cart);
      $em->flush();

      return $this->redirectToRoute('app_cart');
    } catch (\Exception $e) {
      $this->addFlash('error', 'Une erreur s\'est produite ' . $e->getMessage());
      return $this->redirectToRoute('app_cart');
    }
  }

  /**
   * Deletes an item from the user's cart.
   *
   * @param Request $request The request object
   * @param EntityManagerInterface $em The Entity Manager instance
   *
   * @return Response Redirects to the cart page
   */
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

      // Get the cart item from the request
      $cartId = $request->request->get('id');
      $cart = $em->getRepository(Cart::class)->find($cartId);

      // Delete the cart item
      if ($cart) {
        $em->remove($cart);
        $em->flush();
      } else {
        $this->addFlash('error', 'Item not found');
      }
    } catch (\Exception $e) {
      $this->addFlash('error', 'Une erreur s\'est produite ' . $e->getMessage());
    }

    return $this->redirectToRoute('app_cart');
  }
}
