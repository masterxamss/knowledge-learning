<?php

namespace App\Entity;

use App\Repository\CartRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents an item added to the user's shopping cart.
 *
 * Each cart entry can be associated with either a course or a lesson, and includes
 * pricing details such as price, discount, subtotal, tax (TVA), and total.
 *
 */
#[ORM\Entity(repositoryClass: CartRepository::class)]
class Cart
{
  /**
   * The unique identifier of the cart item.
   *
   * @var int|null
   */
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  /**
   * The user associated with this cart item.
   *
   * @var User|null
   */
  #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'cart')]
  #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
  private ?User $user = null;

  /**
   * The course associated with this cart item.
   *
   * @var Courses|null
   */
  #[ORM\ManyToOne(targetEntity: Courses::class, inversedBy: 'cart')]
  #[ORM\JoinColumn(name: 'course_id', referencedColumnName: 'id', nullable: true)]
  private ?Courses $course = null;

  /**
   * The lesson associated with this cart item.
   *
   * @var Lessons|null
   */
  #[ORM\ManyToOne(targetEntity: Lessons::class, inversedBy: 'cart')]
  #[ORM\JoinColumn(name: 'lesson_id', referencedColumnName: 'id', nullable: true)]
  private ?Lessons $lesson = null;

  /**
   * The price of the cart item.
   *
   * @var string|null
   */
  #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
  private ?string $price = null;

  /**
   * The discount applied to the cart item.
   *
   * @var string|null
   */
  #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
  private ?string $discount = null;

  /**
   * The subtotal amount for the cart item.
   *
   * @var string|null
   */
  #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
  private ?string $subTotal = null;

  /**
   * The TVA (tax value added) amount for the cart item.
   *
   * @var string|null
   */
  #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
  private ?string $tva = null;

  /**
   * The total amount for the cart item.
   *
   * @var string|null
   */
  #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
  private ?string $total = null;

  /**
   * Gets the ID of the cart item.
   *
   * @return int|null
   */
  public function getId(): ?int
  {
    return $this->id;
  }

  /**
   * Gets the user associated with this cart item.
   *
   * @return User|null
   */
  public function getUser(): ?User
  {
    return $this->user;
  }

  /**
   * Sets the user associated with this cart item.
   *
   * @param User $user
   * @return static
   */
  public function setUser(User $user): static
  {
    $this->user = $user;

    return $this;
  }

  /**
   * Gets the course associated with this cart item.
   *
   * @return Courses|null
   */
  public function getCourse(): ?Courses
  {
    return $this->course;
  }

  /**
   * Sets the course associated with this cart item.
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
   * Gets the lesson associated with this cart item.
   *
   * @return Lessons|null
   */
  public function getLesson(): ?Lessons
  {
    return $this->lesson;
  }

  /**
   * Sets the lesson associated with this cart item.
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
   * Gets the price of the cart item.
   *
   * @return string|null
   */
  public function getPrice(): ?string
  {
    return $this->price;
  }

  /**
   * Sets the price of the cart item.
   *
   * @param string $price
   * @return static
   */
  public function setPrice(string $price): static
  {
    $this->price = $price;

    return $this;
  }

  /**
   * Gets the discount applied to the cart item.
   *
   * @return string|null
   */
  public function getDiscount(): ?string
  {
    return $this->discount;
  }

  /**
   * Sets the discount applied to the cart item.
   *
   * @param string|null $discount
   * @return static
   */
  public function setDiscount(?string $discount): static
  {
    $this->discount = $discount;

    return $this;
  }

  /**
   * Gets the subtotal amount for the cart item.
   *
   * @return string|null
   */
  public function getSubTotal(): ?string
  {
    return $this->subTotal;
  }

  /**
   * Sets the subtotal amount for the cart item.
   *
   * @param string|null $subTotal
   * @return static
   */
  public function setSubTotal(?string $subTotal): static
  {
    $this->subTotal = $subTotal;

    return $this;
  }

  /**
   * Gets the TVA (tax value added) amount for the cart item.
   *
   * @return string|null
   */
  public function getTva(): ?string
  {
    return $this->tva;
  }

  /**
   * Sets the TVA (tax value added) amount for the cart item.
   *
   * @param string $tva
   * @return static
   */
  public function setTva(string $tva): static
  {
    $this->tva = $tva;

    return $this;
  }

  /**
   * Gets the total amount for the cart item.
   *
   * @return string|null
   */
  public function getTotal(): ?string
  {
    return $this->total;
  }

  /**
   * Sets the total amount for the cart item.
   *
   * @param string $total
   * @return static
   */
  public function setTotal(string $total): static
  {
    $this->total = $total;

    return $this;
  }
}
