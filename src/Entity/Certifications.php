<?php

namespace App\Entity;

use App\Repository\CertificationsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a certification awarded to a user for completing a course.
 *
 * A certification links a user to a course and records the date the certification was issued.
 *
 */
#[ORM\Entity(repositoryClass: CertificationsRepository::class)]
class Certifications
{
  /**
   * The unique identifier of the certification.
   *
   * @var int|null
   */
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  /**
   * The user who received the certification.
   *
   * @var User|null
   */
  #[ORM\ManyToOne(inversedBy: 'certifications')]
  #[ORM\JoinColumn(nullable: false)]
  private ?User $user = null;

  /**
   * The course for which the certification was awarded.
   *
   * @var Courses|null
   */
  #[ORM\ManyToOne(inversedBy: 'certifications')]
  #[ORM\JoinColumn(nullable: false)]
  private ?Courses $course = null;

  /**
   * The date the certification was issued.
   *
   * @var \DateTimeImmutable|null
   */
  #[ORM\Column]
  private ?\DateTimeImmutable $date = null;

  /**
   * Gets the ID of the certification.
   *
   * @return int|null
   */
  public function getId(): ?int
  {
    return $this->id;
  }

  /**
   * Gets the user who received the certification.
   *
   * @return User|null
   */
  public function getUser(): ?User
  {
    return $this->user;
  }

  /**
   * Sets the user who received the certification.
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
   * Gets the course for which the certification was awarded.
   *
   * @return Courses|null
   */
  public function getCourse(): ?Courses
  {
    return $this->course;
  }

  /**
   * Sets the course for which the certification was awarded.
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
   * Gets the date the certification was issued.
   *
   * @return \DateTimeImmutable|null
   */
  public function getDate(): ?\DateTimeImmutable
  {
    return $this->date;
  }

  /**
   * Sets the date the certification was issued.
   *
   * @param \DateTimeImmutable $date
   * @return static
   */
  public function setDate(\DateTimeImmutable $date): static
  {
    $this->date = $date;
    return $this;
  }
}
