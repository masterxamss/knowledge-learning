<?php

namespace App\Entity;

use App\Repository\OrderItemRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderItemRepository::class)]
class OrderItem
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'orderItems')]
  #[ORM\JoinColumn(name: 'order_id', referencedColumnName: 'id',  nullable: false)]
  private ?Order $orders = null;

  #[ORM\ManyToOne(targetEntity: Courses::class, inversedBy: 'orderItems')]
  #[ORM\JoinColumn(name: 'course_id', referencedColumnName: 'id',  nullable: true)]
  private ?Courses $course = null;

  #[ORM\ManyToOne(targetEntity: Lessons::class,  inversedBy: 'orderItems')]
  #[ORM\JoinColumn(name: 'lesson_id', referencedColumnName: 'id',  nullable: true)]
  private ?Lessons $lesson = null;

  #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
  private ?string $price = null;

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getOrders(): ?Order
  {
    return $this->orders;
  }

  public function setOrders(?Order $orders): static
  {
    $this->orders = $orders;

    return $this;
  }

  public function getCourse(): ?Courses
  {
    return $this->course;
  }

  public function setCourse(?Courses $course): static
  {
    $this->course = $course;

    return $this;
  }

  public function getLesson(): ?Lessons
  {
    return $this->lesson;
  }

  public function setLesson(?Lessons $lesson): static
  {
    $this->lesson = $lesson;

    return $this;
  }

  public function getPrice(): ?string
  {
    return $this->price;
  }

  public function setPrice(string $price): static
  {
    $this->price = $price;

    return $this;
  }
}
