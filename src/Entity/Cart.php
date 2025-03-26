<?php

namespace App\Entity;

use App\Repository\CartRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CartRepository::class)]
class Cart
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'cart')]
  #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
  private ?User $user = null;

  #[ORM\ManyToOne(targetEntity: Courses::class, inversedBy: 'cart')]
  #[ORM\JoinColumn(name: 'course_id', referencedColumnName: 'id', nullable: true)]
  private ?Courses $course = null;

  #[ORM\ManyToOne(targetEntity: Lessons::class, inversedBy: 'cart')]
  #[ORM\JoinColumn(name: 'lesson_id', referencedColumnName: 'id',  nullable: true)]
  private ?Lessons $lesson = null;

  #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
  private ?string $price = null;

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getUser(): ?User
  {
    return $this->user;
  }

  public function setUser(User $user): static
  {
    $this->user = $user;

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
