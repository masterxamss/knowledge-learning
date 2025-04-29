<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents an Order entity in the system.
 * 
 * This entity represents a customer's order. It stores information about
 * the order, such as the user who placed it, the payment details, the total
 * price, and the creation date.
 *
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 */
#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
  /**
   * @var int|null The unique identifier of the order.
   */
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  /**
   * @var User|null The user associated with the order.
   */
  #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'orders')]
  #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
  private ?User $user = null;

  /**
   * @var string|null The payment identifier for the order.
   */
  #[ORM\Column(length: 255, nullable: true)]
  private ?string $paymentId = null;

  /**
   * @var string|null The payment status for the order.
   */
  #[ORM\Column(type: 'string', length: 20, nullable: true)]
  private ?string $paymentStatus = null;

  /**
   * @var string|null The total price of the order.
   */
  #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
  private ?string $totalPrice = null;

  /**
   * @var \DateTimeImmutable|null The creation date of the order.
   */
  #[ORM\Column]
  private ?\DateTimeImmutable $createdAt = null;

  /**
   * Get the unique identifier of the order.
   *
   * @return int|null
   */
  public function getId(): ?int
  {
    return $this->id;
  }

  /**
   * Get the user associated with the order.
   *
   * @return User|null
   */
  public function getUser(): ?User
  {
    return $this->user;
  }

  /**
   * Set the user associated with the order.
   *
   * @param User|null $user
   * @return static
   */
  public function setUser(?User $user): static
  {
    $this->user = $user;

    return $this;
  }

  /**
   * Get the payment identifier for the order.
   *
   * @return string|null
   */
  public function getPaymentId(): ?string
  {
    return $this->paymentId;
  }

  /**
   * Set the payment identifier for the order.
   *
   * @param string $paymentId
   * @return static
   */
  public function setPaymentId(string $paymentId): static
  {
    $this->paymentId = $paymentId;

    return $this;
  }

  /**
   * Get the payment status for the order.
   *
   * @return string|null
   */
  public function getPaymentStatus(): ?string
  {
    return $this->paymentStatus;
  }

  /**
   * Set the payment status for the order.
   *
   * @param string $paymentStatus
   * @return static
   */
  public function setPaymentStatus(string $paymentStatus): static
  {
    $this->paymentStatus = $paymentStatus;

    return $this;
  }

  /**
   * Get the total price of the order.
   *
   * @return string|null
   */
  public function getTotalPrice(): ?string
  {
    return $this->totalPrice;
  }

  /**
   * Set the total price of the order.
   *
   * @param string $totalPrice
   * @return static
   */
  public function setTotalPrice(string $totalPrice): static
  {
    $this->totalPrice = $totalPrice;

    return $this;
  }

  /**
   * Get the creation date of the order.
   *
   * @return \DateTimeImmutable|null
   */
  public function getCreatedAt(): ?\DateTimeImmutable
  {
    return $this->createdAt;
  }

  /**
   * Set the creation date of the order.
   *
   * @param \DateTimeImmutable $createdAt
   * @return static
   */
  public function setCreatedAt(\DateTimeImmutable $createdAt): static
  {
    $this->createdAt = $createdAt;

    return $this;
  }
}
