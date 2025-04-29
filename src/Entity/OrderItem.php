<?php

namespace App\Entity;

use App\Repository\OrderItemRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents an item in an order.
 * 
 * This entity stores the details of an item within an order, which can be either
 * a course or a lesson. It includes the associated order, the course or lesson,
 * and the price of the item.
 *
 * @ORM\Entity(repositoryClass=OrderItemRepository::class)
 */
#[ORM\Entity(repositoryClass: OrderItemRepository::class)]
class OrderItem
{
  /**
   * @var int|null The unique identifier of the order item.
   */
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  /**
   * @var Order|null The order to which this item belongs.
   */
  #[ORM\ManyToOne(targetEntity: Order::class)]
  #[ORM\JoinColumn(name: 'order_id', referencedColumnName: 'id', nullable: false)]
  private ?Order $orders = null;

  /**
   * @var Courses|null The course associated with the order item (if applicable).
   */
  #[ORM\ManyToOne(targetEntity: Courses::class, inversedBy: 'orderItems')]
  #[ORM\JoinColumn(name: 'course_id', referencedColumnName: 'id', nullable: true)]
  private ?Courses $course = null;

  /**
   * @var Lessons|null The lesson associated with the order item (if applicable).
   */
  #[ORM\ManyToOne(targetEntity: Lessons::class, inversedBy: 'orderItems')]
  #[ORM\JoinColumn(name: 'lesson_id', referencedColumnName: 'id', nullable: true)]
  private ?Lessons $lesson = null;

  /**
   * @var string|null The price of the order item.
   */
  #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
  private ?string $price = null;

  /**
   * Get the unique identifier of the order item.
   *
   * @return int|null
   */
  public function getId(): ?int
  {
    return $this->id;
  }

  /**
   * Get the order to which this item belongs.
   *
   * @return Order|null
   */
  public function getOrders(): ?Order
  {
    return $this->orders;
  }

  /**
   * Set the order to which this item belongs.
   *
   * @param Order|null $orders
   * @return static
   */
  public function setOrders(?Order $orders): static
  {
    $this->orders = $orders;

    return $this;
  }

  /**
   * Get the course associated with the order item.
   *
   * @return Courses|null
   */
  public function getCourse(): ?Courses
  {
    return $this->course;
  }

  /**
   * Set the course associated with the order item.
   *
   * @param Courses|null $course
   * @return static
   */
  public function setCourse(?Courses $course): static
  {
    $this->course = $course;

    return $this;
  }

  /**
   * Get the lesson associated with the order item.
   *
   * @return Lessons|null
   */
  public function getLesson(): ?Lessons
  {
    return $this->lesson;
  }

  /**
   * Set the lesson associated with the order item.
   *
   * @param Lessons|null $lesson
   * @return static
   */
  public function setLesson(?Lessons $lesson): static
  {
    $this->lesson = $lesson;

    return $this;
  }

  /**
   * Get the price of the order item.
   *
   * @return string|null
   */
  public function getPrice(): ?string
  {
    return $this->price;
  }

  /**
   * Set the price of the order item.
   *
   * @param string $price
   * @return static
   */
  public function setPrice(string $price): static
  {
    $this->price = $price;

    return $this;
  }
}
