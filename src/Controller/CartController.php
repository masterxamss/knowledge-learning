<?php

namespace App\Controller;

use App\Entity\Courses;
use App\Entity\Lessons;
use App\Entity\User;
use App\Entity\Cart;
use App\Entity\OrderItem;

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
    $subTotal = 0;
    $totalDiscount = 0;
    $totalTva = 0;

    $getCart = $em->getRepository(Cart::class)->findBy(['user' => $this->getUser()]);

    if (!$getCart) {
      $this->addFlash('info', 'Votre panier est vide');
    }

    foreach ($getCart as $cart) {
      $totalTva += $cart->getTva();
      $totalDiscount += $cart->getDiscount();
      $subTotal += $cart->getSubTotal();
      $total += $cart->getTotal();
    }

    return $this->render('cart/cart.html.twig', [
      'cart' => $getCart,
      'subTotal' => number_format($subTotal, 2, '.', ''),
      'totalDiscount' => number_format($totalDiscount, 2, '.', ''),
      'totalTva' => number_format($totalTva, 2, '.', ''),
      'total' => number_format($total, 2, '.', ''),
    ]);
  }

  #[Route('/cart/add-item', name: 'app_cart_add', methods: ['POST'])]
  public function addCartItem(Request $request, EntityManagerInterface $em): Response
  {
    try {

      // Validar o token CSRF
      $submittedToken = $request->request->get('token');
      if (!$this->isCsrfTokenValid('cart-add', $submittedToken)) {
        $this->addFlash('error', 'Invalid CSRF token');
        return $this->redirectToRoute('app_cart');
      }

      // Obter o utilizador autenticado
      $user = $this->getUser();
      if (!$user) {
        $this->addFlash('info', 'Vous devez vous connecter pour effectuer cette action');
        return $this->redirectToRoute('app_home');
      }

      // Verify if the user is verified
      if (!$user->getIsVerified()) {
        $this->addFlash('info', 'Verifier votre email pour pouvoir acheter');
        return $this->redirectToRoute('app_home');
      }

      // Obter IDs da requisição
      $lessonId = $request->request->get('lesson_id');
      $courseId = $request->request->get('course_id');

      // Criar o item do carrinho
      $cart = new Cart();
      $cart->setUser($user);

      if ($lessonId) {
        // Verificar se a lição já está no carrinho
        if ($em->getRepository(Cart::class)->findOneBy(['lesson' => $lessonId, 'user' => $user])) {
          return $this->redirectToRoute('app_cart');
        }

        $lesson = $em->getRepository(Lessons::class)->find($lessonId);
        if (!$lesson) {
          $this->addFlash('error', 'Lição não encontrada');
          return $this->redirectToRoute('app_cart');
        }

        // Impedir adicionar uma lição se o curso inteiro já está no carrinho
        if ($em->getRepository(Cart::class)->findOneBy(['course' => $lesson->getCourse(), 'user' => $user])) {
          $this->addFlash('info', 'Le cours contenant cette leçon se trouve déjà dans le panier');
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

      if ($courseId) {
        // Verificar se o curso já está no carrinho
        if ($em->getRepository(Cart::class)->findOneBy(['course' => $courseId, 'user' => $user])) {
          return $this->redirectToRoute('app_cart');
        }

        $course = $em->getRepository(Courses::class)->find($courseId);
        if (!$course) {
          $this->addFlash('error', 'Curso não encontrado');
          return $this->redirectToRoute('app_cart');
        }

        // Remover lições do curso já no carrinho
        $cartLessons = $em->getRepository(Cart::class)->findBy(['user' => $user]);
        $lessonsBought = 0;
        foreach ($cartLessons as $item) {
          if ($item->getLesson() && $item->getLesson()->getCourse()->getId() === $course->getId()) {
            $em->remove($item);
          }
        }

        // Verificar lições já compradas
        //$boughtLessons = $em->getRepository(OrderItem::class)->findBy(['orders.user' => $user, 'lesson.course' => $course]);

        $boughtLessons = $em->getRepository(OrderItem::class)->findOrderByLessonCourse($course->getId());
        foreach ($boughtLessons as $lesson) {
          $lessonsBought += $lesson->getLesson()->getPrice();
        }


        // Aplicar desconto se necessário
        $cart->setPrice($course->getPrice());
        $cart->setDiscount($lessonsBought);
        $subTotal = $course->getPrice() - $lessonsBought;
        $cart->setSubTotal(max($subTotal, 0)); // Garantir que não fique negativo

        // Calcular TVA e preço final
        $totalTva = $subTotal * 0.20;
        $cart->setTva($totalTva);
        $cart->setTotal($subTotal + $totalTva);

        $cart->setCourse($course);
      }

      // Persistir no banco de dados
      $em->persist($cart);
      $em->flush();

      return $this->redirectToRoute('app_cart');
    } catch (\Exception $e) {
      $this->addFlash('error', 'Une erreur est survenue lors de l\'ajout au panier: ' . $e->getMessage());
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
