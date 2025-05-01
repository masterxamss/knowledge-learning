<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Cart;
use App\Entity\OrderItem;
use App\Entity\User;
use App\Entity\Completion;
use App\Entity\Lessons;
use App\Security\Voter\UserVoter;
use App\Repository\OrderRepository;
use App\Service\StripeServiceInterface;
use App\Repository\LessonsRepository;
use App\Repository\CoursesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Controller for handling the checkout process and payment via Stripe.
 *
 * This controller manages the checkout process, including:
 * - Verifying the user's cart.
 * - Creating an order with associated order items.
 * - Calculating the total price of the cart.
 * - Creating a Stripe payment link.
 * - Handling success, cancellation, and errors during the checkout.
 *
 */
#[IsGranted('ROLE_USER')]
class CheckOutController extends AbstractController
{
  /**
   * Constructor to inject dependencies.
   *
   * @param StripeServiceInterface $stripeServiceInterface Stripe service for payment handling.
   * @param EntityManagerInterface $entityManagerInterface Doctrine's EntityManager for DB operations.
   * @param OrderRepository $orderRepository Repository for managing orders.
   * @param LessonsRepository $lessonsRepository Repository for managing lessons.
   * @param CoursesRepository $coursesRepository Repository for managing courses.
   */
  public function __construct(
    private readonly StripeServiceInterface $stripeServiceInterface,
    private readonly EntityManagerInterface $entityManagerInterface,
    private readonly OrderRepository $orderRepository,
    private LessonsRepository $lessonsRepository,
    private CoursesRepository $coursesRepository
  ) {
    $this->coursesRepository = $coursesRepository;
    $this->lessonsRepository = $lessonsRepository;
  }

  /**
   * Handles the checkout process, including payment via Stripe.
   *
   * This function:
   * - Verifies the user's cart.
   * - Creates an order and order items.
   * - Calculates the total price of the cart.
   * - Creates a Stripe payment link.
   * - Rolls back or commits the database transaction based on success or failure.
   *
   * @return Response Redirects to Stripe payment URL or the cart page with an error message.
   */
  #[Route('/checkout', name: 'app_stripe', methods: ['GET', 'POST'])]
  public function checkout(): Response
  {
    $user = $this->getUser();
    $cartItems = $this->entityManagerInterface->getRepository(Cart::class)->findBy(['user' => $user]);

    if (!$cartItems) {
      return $this->redirectToRoute('app_cart');
    }

    // Begin Transaction
    $this->entityManagerInterface->getConnection()->beginTransaction();

    try {
      // Create Order and associate it with the user
      $order = new Order();
      $order->setUser($user);
      $order->setPaymentStatus('pending');
      $order->setCreatedAt(new \DateTimeImmutable());
      $this->entityManagerInterface->persist($order);

      $data = [];
      $total = 0;

      // Process Cart Items
      foreach ($cartItems as $cart) {
        $orderItem = new OrderItem();
        $orderItem->setOrders($order);

        if ($cart->getLesson()) {
          $product = $cart->getLesson();
          $orderItem->setLesson($product);
          $price = $cart->getTotal();
        } elseif ($cart->getCourse()) {
          $product = $cart->getCourse();
          $orderItem->setCourse($product);
          $price = $cart->getTotal();
        }

        // Calculate total and prepare Stripe data
        $quantity = 1;
        $total += $price * $quantity;

        $data[] = [
          'product' => $product->getTitle(),
          'price' => $price,
          'quantity' => $quantity,
        ];

        $orderItem->setPrice($price);
        $this->entityManagerInterface->persist($orderItem);
      }

      // Update order total price and persist
      $order->setTotalPrice($total);
      $this->entityManagerInterface->flush();

      // Create Stripe payment and redirect
      $url = $this->stripeServiceInterface->createPayment($data, $order);
      $this->entityManagerInterface->getConnection()->commit();

      return $this->redirect($url, Response::HTTP_SEE_OTHER);
    } catch (\Exception $e) {
      // Rollback transaction in case of error
      $this->entityManagerInterface->getConnection()->rollBack();
      $this->addFlash('error', 'An error occurred. Please try again.' . $e->getMessage());

      return $this->redirectToRoute('app_cart');
    }
  }

  /**
   * Handles the success callback after Stripe payment.
   *
   * This function:
   * - Updates the order status to success.
   * - Cleans up the cart after successful payment.
   * - Creates completion entries for the purchased lessons or courses.
   *
   * @param Order $order The order object.
   * @param User $user The authenticated user.
   * @param int $id The order ID.
   * @return Response Renders the success page with order details.
   */
  #[Route('/checkout/success/{order}/{id}', name: 'app_stripe_success')]
  #[IsGranted(UserVoter::EDIT, subject: 'user')]
  public function success(Order $order, User $user, int $id): Response
  {
    try {
      // Update order status to success
      $order->setPaymentStatus('success');
      $order->setPaymentId($this->stripeServiceInterface->getPaymentId());
      $this->entityManagerInterface->persist($order);

      // Clean the user's cart
      $cartItems = $this->entityManagerInterface->getRepository(Cart::class)->findBy(['user' => $user]);
      foreach ($cartItems as $cart) {
        $this->entityManagerInterface->remove($cart);
      }

      // Create user completion records for lessons or courses
      $orderItem = $this->entityManagerInterface->getRepository(OrderItem::class)->findBy(['orders' => $order->getId()]);
      foreach ($orderItem as $item) {
        if ($item->getCourse()) {
          $getCourseLessons = $this->entityManagerInterface->getRepository(Lessons::class)->findLessonsByCourse($item->getCourse()->getId());
          foreach ($getCourseLessons as $lesson) {
            $completion = new Completion();
            $completion->setLesson($lesson);
            $completion->setUser($user);
            $completion->setStatus('in-progress');
            $completion->setCompletionDate(null);
            $this->entityManagerInterface->persist($completion);
          }
        } elseif ($item->getLesson()) {
          $completion = new Completion();
          $completion->setLesson($item->getLesson());
          $completion->setUser($user);
          $completion->setStatus('in-progress');
          $completion->setCompletionDate(null);
          $this->entityManagerInterface->persist($completion);
        }
      }

      $this->entityManagerInterface->flush();

      // Render success page
      $orderItems = $this->entityManagerInterface->getRepository(OrderItem::class)->findBy(['orders' => $order->getId()]);
      $this->addFlash('success', 'Your purchase was successful.');

      return $this->render('checkout/success.html.twig', [
        'order' => $order,
        'orderItems' => $orderItems,
      ]);
    } catch (\Exception $e) {
      // Handle error and redirect to cart
      $this->addFlash('error', 'An error occurred. Please try again.' . $e->getMessage());
      return $this->redirectToRoute('app_cart');
    }
  }

  /**
   * Handles the cancellation of the checkout process.
   *
   * This function:
   * - Removes the order and its items from the database.
   * - Redirects the user to the cart page.
   *
   * @param Order $order The order object to cancel.
   * @return Response Redirects to the cart page.
   */
  #[Route('/checkout/cancel/{order}', name: 'app_stripe_cancel')]
  public function cancel(Order $order): Response
  {
    try {
      // Remove order items and the order itself from the database
      $getOrderItems = $this->entityManagerInterface->getRepository(OrderItem::class)->findBy(['orders' => $order->getId()]);
      foreach ($getOrderItems as $orderItem) {
        $this->entityManagerInterface->remove($orderItem);
      }
      $this->entityManagerInterface->remove($order);
      $this->entityManagerInterface->flush();

      return $this->redirectToRoute('app_cart');
    } catch (\Exception $e) {
      // Handle error and redirect to cart
      $this->addFlash('error', 'An error occurred. Please try again.' . $e->getMessage());
      return $this->redirectToRoute('app_cart');
    }
  }
}
