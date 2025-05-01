<?php

namespace App\Service;

/**
 * Interface StripeServiceInterface
 *
 * Defines the contract for interacting with the Stripe payment system.
 * Any service implementing this interface should handle payment creation
 * and retrieval of payment-related data for a specific order.
 *
 * @package App\Service
 */
interface StripeServiceInterface
{
  /**
   * Creates a Stripe payment session based on the given cart and order ID.
   *
   * This method is responsible for initializing a payment process and
   * returning a URL or session ID used for redirecting the user to Stripe's checkout.
   *
   * @param mixed $cart     The cart object or data structure containing items to be purchased.
   * @param int|string $orderId The unique identifier of the order associated with the payment.
   *
   * @return string A URL or session ID that initiates the Stripe checkout process.
   */
  public function createPayment($cart, $orderId): string;

  /**
   * Returns the Stripe payment/session ID created during the payment process.
   *
   * This ID can be used to reference the payment session in further operations.
   *
   * @return mixed The payment or session ID from Stripe.
   */
  public function getPaymentId(): mixed;

  /**
   * Returns the order ID associated with the Stripe payment session.
   *
   * This method allows retrieval of the order information linked to a given payment.
   *
   * @return mixed The order ID linked to the payment session.
   */
  public function getPaymentOrder(): mixed;
}
