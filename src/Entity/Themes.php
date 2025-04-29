<?php

namespace App\Entity;

use App\Repository\ThemesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Doctrine\ORM\Mapping as ORM;

/**
 * Represents a theme grouping multiple courses.
 *
 * A Theme has a name, description, image, creation date, slug, title,
 * and can be highlighted. It can be associated with multiple courses.
 *
 * @ORM\Entity(repositoryClass=ThemesRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
#[ORM\Entity(repositoryClass: ThemesRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Themes
{
  /**
   * @var int|null The unique identifier of the theme.
   */
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  /**
   * @var string|null The name of the theme.
   */
  #[ORM\Column(length: 255)]
  private ?string $name = null;

  /**
   * @var string|null A detailed description of the theme.
   */
  #[ORM\Column(type: 'text', nullable: true, options: ['columnDefinition' => 'LONGTEXT'])]
  private ?string $description = null;

  /**
   * @var string|null The filename or path of the theme's image.
   */
  #[ORM\Column(length: 255, nullable: true)]
  private ?string $image = null;

  /**
   * @var \DateTimeImmutable|null The date and time when the theme was created.
   */
  #[ORM\Column]
  private ?\DateTimeImmutable $created_at = null;

  /**
   * @var string|null The slug generated from the theme's name.
   */
  #[ORM\Column(length: 255)]
  private ?string $slug = null;

  /**
   * @var string|null The title of the theme.
   */
  #[ORM\Column(length: 255)]
  private ?string $title = null;

  /**
   * @var Collection<int, Courses> The courses associated with this theme.
   */
  #[ORM\OneToMany(targetEntity: Courses::class, mappedBy: 'theme')]
  private Collection $courses;

  /**
   * @var bool|null Whether the theme is highlighted.
   */
  #[ORM\Column(nullable: true)]
  private ?bool $highlight = null;

  /**
   * Constructor initializes the courses collection and sets the creation date.
   */
  public function __construct()
  {
    $this->courses = new ArrayCollection();
    $this->created_at = new \DateTimeImmutable();
  }

  /**
   * Generates the slug automatically based on the name before persisting or updating.
   *
   * @return void
   */
  #[ORM\PrePersist]
  #[ORM\PreUpdate]
  public function generateSlug(): void
  {
    if (!empty($this->name)) {
      $this->slug = (new AsciiSlugger())->slug($this->name)->lower();
    }
  }

  /**
   * Get the unique identifier of the theme.
   *
   * @return int|null
   */
  public function getId(): ?int
  {
    return $this->id;
  }

  /**
   * Get the name of the theme.
   *
   * @return string|null
   */
  public function getName(): ?string
  {
    return $this->name;
  }

  /**
   * Set the name of the theme.
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
   * Returns the name of the theme as a string.
   *
   * @return string
   */
  public function __toString(): string
  {
    return $this->name;
  }

  /**
   * Get the description of the theme.
   *
   * @return string|null
   */
  public function getDescription(): ?string
  {
    return $this->description;
  }

  /**
   * Set the description of the theme.
   *
   * @param string $description
   * @return static
   */
  public function setDescription(string $description): static
  {
    $this->description = $description;

    return $this;
  }

  /**
   * Get the image filename or path.
   *
   * @return string|null
   */
  public function getImage(): ?string
  {
    return $this->image;
  }

  /**
   * Set the image filename or path.
   *
   * @param string|null $image
   * @return static
   */
  public function setImage(?string $image): static
  {
    $this->image = $image;

    return $this;
  }

  /**
   * Get the creation date of the theme.
   *
   * @return \DateTimeImmutable|null
   */
  public function getCreatedAt(): ?\DateTimeImmutable
  {
    return $this->created_at;
  }

  /**
   * Set the creation date of the theme.
   *
   * @param \DateTimeImmutable $created_at
   * @return static
   */
  public function setCreatedAt(\DateTimeImmutable $created_at): static
  {
    $this->created_at = $created_at;

    return $this;
  }

  /**
   * Get the slug of the theme.
   *
   * @return string|null
   */
  public function getSlug(): ?string
  {
    return $this->slug;
  }

  /**
   * Set the slug of the theme.
   *
   * @param string $slug
   * @return static
   */
  public function setSlug(string $slug): static
  {
    $this->slug = $slug;

    return $this;
  }

  /**
   * Get the title of the theme.
   *
   * @return string|null
   */
  public function getTitle(): ?string
  {
    return $this->title;
  }

  /**
   * Set the title of the theme.
   *
   * @param string $title
   * @return static
   */
  public function setTitle(string $title): static
  {
    $this->title = $title;

    return $this;
  }

  /**
   * Get the courses associated with the theme.
   *
   * @return Collection<int, Courses>
   */
  public function getCourses(): Collection
  {
    return $this->courses;
  }

  /**
   * Add a course to the theme.
   *
   * @param Courses $course
   * @return static
   */
  public function addCourse(Courses $course): static
  {
    if (!$this->courses->contains($course)) {
      $this->courses->add($course);
      $course->setTheme($this);
    }

    return $this;
  }

  /**
   * Remove a course from the theme.
   *
   * @param Courses $course
   * @return static
   */
  public function removeCourse(Courses $course): static
  {
    if ($this->courses->removeElement($course)) {
      // Set the owning side to null (unless already changed)
      if ($course->getTheme() === $this) {
        $course->setTheme(null);
      }
    }

    return $this;
  }

  /**
   * Check if the theme is highlighted.
   *
   * @return bool|null
   */
  public function isHighlight(): ?bool
  {
    return $this->highlight;
  }

  /**
   * Set whether the theme is highlighted.
   *
   * @param bool|null $highlight
   * @return static
   */
  public function setHighlight(?bool $highlight): static
  {
    $this->highlight = $highlight;

    return $this;
  }
}
