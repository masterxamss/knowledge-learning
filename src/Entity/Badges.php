<?php

namespace App\Entity;

use App\Repository\BadgesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a badge that can be assigned to multiple courses.
 *
 * A badge is a mark or symbol of achievement associated with courses. It contains a name,
 * optional CSS class for styling, and timestamps for creation and updates.
 *
 */
#[ORM\Entity(repositoryClass: BadgesRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Badges
{
  /**
   * The unique identifier of the badge.
   *
   * @var int|null
   */
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  /**
   * The name of the badge.
   *
   * @var string|null
   */
  #[ORM\Column(length: 255)]
  private ?string $name = null;

  /**
   * The timestamp when the badge was created.
   *
   * @var \DateTimeImmutable|null
   */
  #[ORM\Column(nullable: true)]
  private ?\DateTimeImmutable $createdAt = null;

  /**
   * The timestamp when the badge was last updated.
   *
   * @var \DateTimeImmutable|null
   */
  #[ORM\Column(nullable: true)]
  private ?\DateTimeImmutable $updatedAt = null;

  /**
   * The list of courses associated with this badge.
   *
   * @var Collection<int, Courses>
   */
  #[ORM\OneToMany(targetEntity: Courses::class, mappedBy: 'badge')]
  private Collection $courses;

  /**
   * An optional CSS class for customizing the badge's appearance.
   *
   * @var string|null
   */
  #[ORM\Column(length: 255, nullable: true)]
  private ?string $cssClass = null;

  /**
   * Constructor.
   * Initializes the courses collection.
   */
  public function __construct()
  {
    $this->courses = new ArrayCollection();
  }

  /**
   * Gets the ID of the badge.
   *
   * @return int|null
   */
  public function getId(): ?int
  {
    return $this->id;
  }

  /**
   * Gets the name of the badge.
   *
   * @return string|null
   */
  public function getName(): ?string
  {
    return $this->name;
  }

  /**
   * Sets the name of the badge.
   *
   * @param string $name
   * @return static
   */
  public function setName(string $name): static
  {
    $this->name = $name;
    return $this;
  }

  /**
   * Returns the badge name when the object is converted to a string.
   *
   * @return string
   */
  public function __toString(): string
  {
    return $this->name;
  }

  /**
   * Gets the creation timestamp.
   *
   * @return \DateTimeImmutable|null
   */
  public function getCreatedAt(): ?\DateTimeImmutable
  {
    return $this->createdAt;
  }

  /**
   * Sets the creation timestamp manually.
   *
   * @param \DateTimeImmutable|null $createdAt
   * @return static
   */
  public function setCreatedAt(?\DateTimeImmutable $createdAt): static
  {
    $this->createdAt = $createdAt;
    return $this;
  }

  /**
   * Gets the last update timestamp.
   *
   * @return \DateTimeImmutable|null
   */
  public function getUpdatedAt(): ?\DateTimeImmutable
  {
    return $this->updatedAt;
  }

  /**
   * Sets the last update timestamp manually.
   *
   * @param \DateTimeImmutable $updatedAt
   * @return static
   */
  public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
  {
    $this->updatedAt = $updatedAt;
    return $this;
  }

  /**
   * Automatically sets the creation timestamp before persisting the entity.
   *
   * @return void
   */
  #[ORM\PrePersist]
  public function setCreatedAtValue(): void
  {
    $this->createdAt = new \DateTimeImmutable();
  }

  /**
   * Automatically sets the update timestamp before updating the entity.
   *
   * @return void
   */
  #[ORM\PreUpdate]
  public function setUpdatedAtValue(): void
  {
    $this->updatedAt = new \DateTimeImmutable();
  }

  /**
   * Gets the courses associated with the badge.
   *
   * @return Collection<int, Courses>
   */
  public function getCourses(): Collection
  {
    return $this->courses;
  }

  /**
   * Associates a course with the badge.
   *
   * @param Courses $course
   * @return static
   */
  public function addCourse(Courses $course): static
  {
    if (!$this->courses->contains($course)) {
      $this->courses->add($course);
      $course->setBadge($this);
    }
    return $this;
  }

  /**
   * Removes a course from the badge.
   *
   * @param Courses $course
   * @return static
   */
  public function removeCourse(Courses $course): static
  {
    if ($this->courses->removeElement($course)) {
      if ($course->getBadge() === $this) {
        $course->setBadge(null);
      }
    }
    return $this;
  }

  /**
   * Gets the CSS class assigned to the badge.
   *
   * @return string|null
   */
  public function getCssClass(): ?string
  {
    return $this->cssClass;
  }

  /**
   * Sets the CSS class for the badge.
   *
   * @param string|null $cssClass
   * @return static
   */
  public function setCssClass(?string $cssClass): static
  {
    $this->cssClass = $cssClass;
    return $this;
  }
}
