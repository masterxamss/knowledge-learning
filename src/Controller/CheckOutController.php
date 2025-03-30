<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Cart;
use App\Entity\OrderItem;
use App\Entity\User;
use App\Security\Voter\UserVoter;
use App\Repository\OrderRepository;
use App\Service\StripeServiceInterface;
//use App\Service\MailService;
use App\Repository\LessonsRepository;
use App\Repository\CoursesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class CheckOutController extends AbstractController
{
  public function __construct(
    private readonly StripeServiceInterface $stripeServiceInterface,
    private readonly EntityManagerInterface $entityManagerInterface,
    private readonly OrderRepository $orderRepository,
    //private readonly MailService $mailService,
    private LessonsRepository $lessonsRepository,
    private CoursesRepository $coursesRepository
  ) {
    $this->coursesRepository = $coursesRepository;
    $this->lessonsRepository = $lessonsRepository;
  }

  /**
   * Route for handling the checkout process and payment via Stripe.
   * 
   * This function manages the user's checkout process by:
   * 1. Checking if the user's cart is empty. If it is, it redirects to the cart page with a flash message.
   * 2. Starting a database transaction to ensure the integrity of the order creation process.
   * 3. Creating an order object and associating it with the logged-in user.
   * 4. Iterating over the cart items, calculating the total price, and creating `OrderItem` objects for each cart item.
   * 5. Persisting the order and order items in the database.
   * 6. Creating a Stripe payment link using the `StripeServiceInterface`.
   * 7. Committing the transaction to finalize the order creation process.
   * 8. If an error occurs during any step, it rolls back the transaction and shows an error message.
   * 
   * @param void
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
      $order = new Order();
      $order->setUser($user);
      $order->setPaymentStatus('pending');
      $order->setCreatedAt(new \DateTimeImmutable());
      $this->entityManagerInterface->persist($order);

      $data = [];
      $total = 0;

      foreach ($cartItems as $cart) {

        // Create OrderItem
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
        // Calculate total and prepare data for Stripe
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

      // Update total price of the order
      $order->setTotalPrice($total);

      $this->entityManagerInterface->flush();

      // Create stripe payment
      $url = $this->stripeServiceInterface->createPayment($data, $order);
      // Confirm transaction
      $this->entityManagerInterface->getConnection()->commit();

      return $this->redirect($url, Response::HTTP_SEE_OTHER);
    } catch (\Exception $e) {
      // Revert transaction in case of error
      $this->entityManagerInterface->getConnection()->rollBack();
      $this->addFlash('error', 'Une erreur est survenue. Veuillez réessayer.' . $e->getMessage());

      return $this->redirectToRoute('app_cart');
    }
  }

  #[Route('/checkout/success/{order}/{id}', name: 'app_stripe_success')]
  #[IsGranted(UserVoter::EDIT, subject: 'user')]
  public function success(Order $order, User $user, int $id): Response
  {
    try {
      // update order status
      $order->setPaymentStatus('success');
      $order->setPaymentId($this->stripeServiceInterface->getPaymentId());
      $this->entityManagerInterface->persist($order);

      // clean cart
      $user = $this->getUser();
      $cartItems = $this->entityManagerInterface->getRepository(Cart::class)->findBy(['user' => $user]);

      if ($cartItems) {
        foreach ($cartItems as $cart) {
          $this->entityManagerInterface->remove($cart);
        }
      }

      $this->entityManagerInterface->flush();

      $orderItems = $this->entityManagerInterface->getRepository(OrderItem::class)->findBy(['orders' => $order->getId()]);

      $this->addFlash('success', 'Votre achat a bien été effectué.');

      return $this->render('checkout/success.html.twig', [
        'order' => $order,
        'orderItems' => $orderItems,
      ]);
    } catch (\Exception $e) {
      $this->addFlash('error', 'Une erreur est survenue. Veuillez réessayer.' . $e->getMessage());
      return $this->redirectToRoute('app_cart');
    }
  }

  #[Route('/checkout/cancel/{order}', name: 'app_stripe_cancel')]
  public function cancel(Order $order): Response
  {
    try {
      $getOrderItems = $this->entityManagerInterface->getRepository(OrderItem::class)->findBy(['orders' => $order->getId()]);
      //dd($getOrderItems);

      //$order->setPaymentStatus('canceled');
      foreach ($getOrderItems as $orderItem) {
        $this->entityManagerInterface->remove($orderItem);
      }
      //$this->entityManagerInterface->persist($order);
      $this->entityManagerInterface->remove($order);
      $this->entityManagerInterface->flush();

      return $this->redirectToRoute('app_cart');
    } catch (\Exception $e) {
      $this->addFlash('error', 'Une erreur est survenue. Veuillez réessayer.' . $e->getMessage());
      return $this->redirectToRoute('app_cart');
    }
  }
}
