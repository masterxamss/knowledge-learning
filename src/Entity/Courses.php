<?php

namespace App\Entity;

use App\Repository\CoursesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\String\Slugger\AsciiSlugger;

/**
 * Represents a course in the system.
 * It stores information such as the course title, description, price, creation and update dates,
 * associated theme, slug for URL generation, and relationships with lessons, order items, and certifications.
 * 
 * @ORM\Entity(repositoryClass=CoursesRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
#[ORM\Entity(repositoryClass: CoursesRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Courses
{
  /**
   * The unique identifier for the course.
   * 
   * @var int|null
   * 
   * @ORM\Id
   * @ORM\GeneratedValue
   * @ORM\Column
   */
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  /**
   * The title of the course.
   * 
   * @var string|null
   * 
   * @ORM\Column(length=255)
   */
  #[ORM\Column(length: 255)]
  private ?string $title = null;

  /**
   * The description of the course.
   * 
   * @var string|null
   * 
   * @ORM\Column(type="text", nullable=true, options={"columnDefinition"="LONGTEXT"})
   */
  #[ORM\Column(type: 'text', nullable: true, options: ['columnDefinition' => 'LONGTEXT'])]
  private ?string $description = null;

  /**
   * The image associated with the course.
   * 
   * @var string|null
   * 
   * @ORM\Column(length=255, nullable=true)
   */
  #[ORM\Column(length: 255, nullable: true)]
  private ?string $image = null;

  /**
   * The price of the course.
   * 
   * @var string|null
   * 
   * @ORM\Column(type=Types::DECIMAL, precision=10, scale=2)
   */
  #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
  private ?string $price = null;

  /**
   * The date when the course was created.
   * 
   * @var \DateTimeImmutable|null
   * 
   * @ORM\Column(nullable=true)
   */
  #[ORM\Column(nullable: true)]
  private ?\DateTimeImmutable $created_at = null;

  /**
   * The date when the course was last updated.
   * 
   * @var \DateTimeImmutable|null
   * 
   * @ORM\Column(nullable=true)
   */
  #[ORM\Column(nullable: true)]
  private ?\DateTimeImmutable $updated_at = null;

  /**
   * The theme associated with the course.
   * 
   * @var Themes|null
   * 
   * @ORM\ManyToOne(targetEntity=Themes::class, inversedBy="courses")
   * @ORM\JoinColumn(name="theme_id", referencedColumnName="id", nullable=false)
   */
  #[ORM\ManyToOne(targetEntity: Themes::class, inversedBy: 'courses')]
  #[ORM\JoinColumn(name: 'theme_id', referencedColumnName: 'id', nullable: false)]
  private ?Themes $theme = null;

  /**
   * The slug for the course (used for URL generation).
   * 
   * @var string|null
   * 
   * @ORM\Column(length=255)
   */
  #[ORM\Column(length: 255)]
  private ?string $slug = null;

  /**
   * The lessons associated with the course.
   * 
   * @var Collection<int, Lessons>
   * 
   * @ORM\OneToMany(targetEntity=Lessons::class, mappedBy="course")
   */
  /**
   * @var Collection<int, Lessons>
   */
  #[ORM\OneToMany(targetEntity: Lessons::class, mappedBy: 'course')]
  private Collection $lessons;

  /**
   * Whether the course is highlighted.
   * 
   * @var bool|null
   * 
   * @ORM\Column(nullable=true)
   */
  #[ORM\Column(nullable: true)]
  private ?bool $highlight = null;

  /**
   * The badge associated with the course.
   * 
   * @var Badges|null
   * 
   * @ORM\ManyToOne(targetEntity=Badges::class, inversedBy="courses")
   * @ORM\JoinColumn(name="badge", referencedColumnName="id", nullable=true)
   */
  #[ORM\ManyToOne(targetEntity: Badges::class, inversedBy: 'courses')]
  #[ORM\JoinColumn(name: 'badge', referencedColumnName: 'id', nullable: true)]
  private ?Badges $badge = null;

  /**
   * The order items associated with the course.
   * 
   * @var Collection<int, OrderItem>
   * 
   * @ORM\OneToMany(targetEntity=OrderItem::class, mappedBy="course")
   */
  /**
   * @var Collection<int, OrderItem>
   */
  #[ORM\OneToMany(targetEntity: OrderItem::class, mappedBy: 'course')]
  private Collection $orderItems;

  /**
   * The certifications associated with the course.
   * 
   * @var Collection<int, Certifications>
   * 
   * @ORM\OneToMany(targetEntity=Certifications::class, mappedBy="course")
   */
  /**
   * @var Collection<int, Certifications>
   */
  #[ORM\OneToMany(targetEntity: Certifications::class, mappedBy: 'course')]
  private Collection $certifications;

  /**
   * Constructor to initialize collections.
   */
  public function __construct()
  {
    $this->lessons = new ArrayCollection();
    $this->orderItems = new ArrayCollection();
    $this->certifications = new ArrayCollection();
  }

  /**
   * Sets the created_at value to the current date when the entity is persisted.
   */
  #[ORM\PrePersist]
  public function setCreatedAtValue(): void
  {
    $this->created_at = new \DateTimeImmutable();
  }

  /**
   * Sets the updated_at value to the current date when the entity is updated.
   */
  #[ORM\PreUpdate]
  public function setUpdatedAtValue(): void
  {
    $this->updated_at = new \DateTimeImmutable();
  }

  /**
   * Generates the slug from the title when the entity is persisted or updated.
   */
  #[ORM\PrePersist]
  #[ORM\PreUpdate]
  public function generateSlug(): void
  {
    if (!empty($this->title)) {
      $this->slug = (new AsciiSlugger())->slug($this->title)->lower();
    }
  }

  /**
   * Gets the ID of the course.
   * 
   * @return int|null The ID of the course.
   */
  public function getId(): ?int
  {
    return $this->id;
  }

  /**
   * Gets the title of the course.
   * 
   * @return string|null The title of the course.
   */
  public function getTitle(): ?string
  {
    return $this->title;
  }

  /**
   * Sets the title of the course.
   * 
   * @param string $title The title of the course.
   * 
   * @return static The current instance of the class.
   */
  public function setTitle(string $title): static
  {
    $this->title = $title;

    return $this;
  }

  /**
   * Returns the string representation of the course.
   * 
   * @return string The title of the course.
   */
  public function __toString(): string
  {
    return $this->title;
  }

  /**
   * Gets the description of the course.
   * 
   * @return string|null The description of the course.
   */
  public function getDescription(): ?string
  {
    return $this->description;
  }

  /**
   * Sets the description of the course.
   * 
   * @param string $description The description of the course.
   * 
   * @return static The current instance of the class.
   */
  public function setDescription(string $description): static
  {
    $this->description = $description;

    return $this;
  }

  /**
   * Gets the image associated with the course.
   * 
   * @return string|null The image associated with the course.
   */
  public function getImage(): ?string
  {
    return $this->image;
  }

  /**
   * Sets the image for the course.
   * 
   * @param string|null $image The image for the course.
   * 
   * @return static The current instance of the class.
   */
  public function setImage(?string $image): static
  {
    $this->image = $image;

    return $this;
  }

  /**
   * Gets the price of the course.
   * 
   * @return string|null The price of the course.
   */
  public function getPrice(): ?string
  {
    return $this->price;
  }

  /**
   * Sets the price of the course.
   * 
   * @param string $price The price of the course.
   * 
   * @return static The current instance of the class.
   */
  public function setPrice(string $price): static
  {
    $this->price = $price;

    return $this;
  }

  /**
   * Gets the creation date of the course.
   * 
   * @return \DateTimeImmutable|null The creation date of the course.
   */
  public function getCreatedAt(): ?\DateTimeImmutable
  {
    return $this->created_at;
  }

  /**
   * Sets the creation date of the course.
   * 
   * @param \DateTimeImmutable|null $created_at The creation date of the course.
   * 
   * @return static The current instance of the class.
   */
  public function setCreatedAt(?\DateTimeImmutable $created_at): static
  {
    $this->created_at = $created_at;

    return $this;
  }

  /**
   * Gets the last update date of the course.
   * 
   * @return \DateTimeImmutable|null The last update date of the course.
   */
  public function getUpdatedAt(): ?\DateTimeImmutable
  {
    return $this->updated_at;
  }

  /**
   * Sets the last update date of the course.
   * 
   * @param \DateTimeImmutable|null $updated_at The last update date of the course.
   * 
   * @return static The current instance of the class.
   */
  public function setUpdatedAt(?\DateTimeImmutable $updated_at): static
  {
    $this->updated_at = $updated_at;

    return $this;
  }

  /**
   * Gets the theme associated with the course.
   * 
   * @return Themes|null The theme associated with the course.
   */
  public function getTheme(): ?Themes
  {
    return $this->theme;
  }

  /**
   * Sets the theme for the course.
   * 
   * @param Themes|null $theme The theme for the course.
   * 
   * @return static The current instance of the class.
   */
  public function setTheme(?Themes $theme): static
  {
    $this->theme = $theme;

    return $this;
  }

  /**
   * Gets the slug of the course.
   * 
   * @return string|null The slug of the course.
   */
  public function getSlug(): ?string
  {
    return $this->slug;
  }

  /**
   * Sets the slug for the course.
   * 
   * @param string $slug The slug for the course.
   * 
   * @return static The current instance of the class.
   */
  public function setSlug(string $slug): static
  {
    $this->slug = $slug;

    return $this;
  }

  /**
   * Gets the lessons associated with the course.
   * 
   * @return Collection<int, Lessons> The lessons associated with the course.
   */
  public function getLessons(): Collection
  {
    return $this->lessons;
  }

  /**
   * Adds a lesson to the course.
   * 
   * @param Lessons $lesson The lesson to add.
   * 
   * @return static The current instance of the class.
   */
  public function addLesson(Lessons $lesson): static
  {
    if (!$this->lessons->contains($lesson)) {
      $this->lessons[] = $lesson;
      $lesson->setCourse($this);
    }

    return $this;
  }

  /**
   * Removes a lesson from the course.
   * 
   * @param Lessons $lesson The lesson to remove.
   * 
   * @return static The current instance of the class.
   */
  public function removeLesson(Lessons $lesson): static
  {
    if ($this->lessons->removeElement($lesson)) {
      if ($lesson->getCourse() === $this) {
        $lesson->setCourse(null);
      }
    }

    return $this;
  }

  /**
   * Gets whether the course is highlighted.
   * 
   * @return bool|null True if the course is highlighted, false otherwise.
   */
  public function isHighlighted(): ?bool
  {
    return $this->highlight;
  }

  /**
   * Sets whether the course is highlighted.
   * 
   * @param bool|null $highlight Whether the course is highlighted.
   * 
   * @return static The current instance of the class.
   */
  public function setHighlight(?bool $highlight): static
  {
    $this->highlight = $highlight;

    return $this;
  }

  /**
   * Gets the badge associated with the course.
   * 
   * @return Badges|null The badge associated with the course.
   */
  public function getBadge(): ?Badges
  {
    return $this->badge;
  }

  /**
   * Sets the badge for the course.
   * 
   * @param Badges|null $badge The badge for the course.
   * 
   * @return static The current instance of the class.
   */
  public function setBadge(?Badges $badge): static
  {
    $this->badge = $badge;

    return $this;
  }

  /**
   * Gets the order items associated with the course.
   * 
   * @return Collection<int, OrderItem> The order items associated with the course.
   */
  public function getOrderItems(): Collection
  {
    return $this->orderItems;
  }

  /**
   * Adds an order item to the course.
   * 
   * @param OrderItem $orderItem The order item to add.
   * 
   * @return static The current instance of the class.
   */
  public function addOrderItem(OrderItem $orderItem): static
  {
    if (!$this->orderItems->contains($orderItem)) {
      $this->orderItems[] = $orderItem;
      $orderItem->setCourse($this);
    }

    return $this;
  }

  /**
   * Removes an order item from the course.
   * 
   * @param OrderItem $orderItem The order item to remove.
   * 
   * @return static The current instance of the class.
   */
  public function removeOrderItem(OrderItem $orderItem): static
  {
    if ($this->orderItems->removeElement($orderItem)) {
      if ($orderItem->getCourse() === $this) {
        $orderItem->setCourse(null);
      }
    }

    return $this;
  }

  /**
   * Gets the certifications associated with the course.
   * 
   * @return Collection<int, Certifications> The certifications associated with the course.
   */
  public function getCertifications(): Collection
  {
    return $this->certifications;
  }

  /**
   * Adds a certification to the course.
   * 
   * @param Certifications $certification The certification to add.
   * 
   * @return static The current instance of the class.
   */
  public function addCertification(Certifications $certification): static
  {
    if (!$this->certifications->contains($certification)) {
      $this->certifications[] = $certification;
      $certification->setCourse($this);
    }

    return $this;
  }

  /**
   * Removes a certification from the course.
   * 
   * @param Certifications $certification The certification to remove.
   * 
   * @return static The current instance of the class.
   */
  public function removeCertification(Certifications $certification): static
  {
    if ($this->certifications->removeElement($certification)) {
      if ($certification->getCourse() === $this) {
        $certification->setCourse(null);
      }
    }

    return $this;
  }
}
