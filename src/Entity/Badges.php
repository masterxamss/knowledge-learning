<?php

namespace App\Entity;

use App\Repository\BadgesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BadgesRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Badges
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(length: 255)]
  private ?string $name = null;

  #[ORM\Column(nullable: true)]
  private ?\DateTimeImmutable $createdAt = null;

  #[ORM\Column(nullable: true)]
  private ?\DateTimeImmutable $updatedAt = null;

  /**
   * @var Collection<int, Courses>
   */
  #[ORM\OneToMany(targetEntity: Courses::class, mappedBy: 'badge')]
  private Collection $courses;

  #[ORM\Column(length: 255, nullable: true)]
  private ?string $cssClass = null;

  public function __construct()
  {
    $this->courses = new ArrayCollection();
  }

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getName(): ?string
  {
    return $this->name;
  }

  public function setName(string $name): static
  {
    $this->name = $name;

    return $this;
  }

  public function __toString(): string
  {
    return $this->name;
  }

  public function getCreatedAt(): ?\DateTimeImmutable
  {
    return $this->createdAt;
  }

  public function setCreatedAt(?\DateTimeImmutable $createdAt): static
  {
    $this->createdAt = $createdAt;

    return $this;
  }

  public function getUpdatedAt(): ?\DateTimeImmutable
  {
    return $this->updatedAt;
  }

  public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
  {
    $this->updatedAt = $updatedAt;

    return $this;
  }

  #[ORM\PrePersist]
  public function setCreatedAtValue(): void
  {
    $this->createdAt = new \DateTimeImmutable();
  }

  #[ORM\PreUpdate]
  public function setUpdatedAtValue(): void
  {
    $this->updatedAt = new \DateTimeImmutable();
  }

  /**
   * @return Collection<int, Courses>
   */
  public function getCourses(): Collection
  {
    return $this->courses;
  }

  public function addCourse(Courses $course): static
  {
    if (!$this->courses->contains($course)) {
      $this->courses->add($course);
      $course->setBadge($this);
    }

    return $this;
  }

  public function removeCourse(Courses $course): static
  {
    if ($this->courses->removeElement($course)) {
      // set the owning side to null (unless already changed)
      if ($course->getBadge() === $this) {
        $course->setBadge(null);
      }
    }

    return $this;
  }

  public function getCssClass(): ?string
  {
      return $this->cssClass;
  }

  public function setCssClass(?string $cssClass): static
  {
      $this->cssClass = $cssClass;

      return $this;
  }
}
