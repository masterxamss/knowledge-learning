<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\ManyToOne(targetEntity: User::class,  inversedBy: 'orders')]
  #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
  private ?User $user = null;

  #[ORM\Column(length: 255, nullable: true)]
  private ?string $paymentId = null;

  #[ORM\Column(type: 'string', length: 20, nullable: true)]
  private ?string $paymentStatus = null;

  #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
  private ?string $totalPrice = null;

  #[ORM\Column]
  private ?\DateTimeImmutable $createdAt = null;

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getUser(): ?User
  {
    return $this->user;
  }

  public function setUser(?User $user): static
  {
    $this->user = $user;

    return $this;
  }

  public function getPaymentId(): ?string
  {
    return $this->paymentId;
  }

  public function setPaymentId(string $paymentId): static
  {
    $this->paymentId = $paymentId;

    return $this;
  }

  public function getPaymentStatus(): ?string
  {
    return $this->paymentStatus;
  }

  public function setPaymentStatus(string $paymentStatus): static
  {
    $this->paymentStatus = $paymentStatus;

    return $this;
  }

  public function getTotalPrice(): ?string
  {
    return $this->totalPrice;
  }

  public function setTotalPrice(string $totalPrice): static
  {
    $this->totalPrice = $totalPrice;

    return $this;
  }

  public function getCreatedAt(): ?\DateTimeImmutable
  {
    return $this->createdAt;
  }

  public function setCreatedAt(\DateTimeImmutable $createdAt): static
  {
    $this->createdAt = $createdAt;

    return $this;
  }
}
