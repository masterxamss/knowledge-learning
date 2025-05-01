<?php

namespace App\Entity;

use App\Repository\LessonsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\String\Slugger\AsciiSlugger;

/**
 * Represents a lesson in the course system.
 * 
 * @ORM\Entity(repositoryClass=LessonsRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
#[ORM\Entity(repositoryClass: LessonsRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Lessons
{
  /**
   * @var int|null The unique ID of the lesson.
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
   * @var string|null The title of the lesson.
   * 
   * @ORM\Column(length=255)
   */
  #[ORM\Column(length: 255)]
  private ?string $title = null;

  /**
   * @var string|null The description of the lesson.
   * 
   * @ORM\Column(type="text", nullable=true, options={"columnDefinition"="LONGTEXT"})
   */
  #[ORM\Column(type: 'text', nullable: true, options: ['columnDefinition' => 'LONGTEXT'])]
  private ?string $description = null;

  /**
   * @var string|null The price of the lesson.
   * 
   * @ORM\Column(type=Types::DECIMAL, precision=10, scale=2)
   */
  #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
  private ?string $price = null;

  /**
   * @var Courses|null The course associated with the lesson.
   * 
   * @ORM\ManyToOne(targetEntity=Courses::class, inversedBy="lessons")
   * @ORM\JoinColumn(name="course_id", referencedColumnName="id", nullable=false)
   */
  #[ORM\ManyToOne(targetEntity: Courses::class, inversedBy: 'lessons')]
  #[ORM\JoinColumn(name: 'course_id', referencedColumnName: 'id', nullable: false)]
  private ?Courses $course = null;

  /**
   * @var string|null The slug generated from the lesson's title.
   * 
   * @ORM\Column(length=255)
   */
  #[ORM\Column(length: 255)]
  private ?string $slug = null;

  /**
   * @var \DateTimeImmutable|null The creation date of the lesson.
   * 
   * @ORM\Column
   */
  #[ORM\Column]
  private ?\DateTimeImmutable $created_at = null;

  /**
   * @var \DateTimeImmutable|null The last update date of the lesson.
   * 
   * @ORM\Column
   */
  #[ORM\Column]
  private ?\DateTimeImmutable $updated_at = null;

  /**
   * @var Collection<int, Chapters> The chapters (sections) of the lesson.
   * 
   * @ORM\OneToMany(targetEntity=Chapters::class, mappedBy="lessonId")
   */
  /**
   * @var Collection<int, Chapters>
   */
  #[ORM\OneToMany(targetEntity: Chapters::class, mappedBy: 'lessonId')]
  private Collection $chapters;

  /**
   * @var bool|null Indicates if the lesson is featured.
   * 
   * @ORM\Column(nullable=true)
   */
  #[ORM\Column(nullable: true)]
  private ?bool $highlight = null;

  /**
   * @var string|null The image path associated with the lesson.
   * 
   * @ORM\Column(length=255, nullable=true)
   */
  #[ORM\Column(length: 255, nullable: true)]
  private ?string $image = null;

  /**
   * @var Badges|null The badge associated with the lesson.
   * 
   * @ORM\ManyToOne(targetEntity=Badges::class)
   * @ORM\JoinColumn(name="badge", referencedColumnName="id", nullable=true)
   */
  #[ORM\ManyToOne(targetEntity: Badges::class)]
  #[ORM\JoinColumn(name: 'badge', referencedColumnName: 'id', nullable: true)]
  private ?Badges $badge = null;

  /**
   * @var Collection<int, OrderItem> The order items associated with this lesson.
   * 
   * @ORM\OneToMany(targetEntity=OrderItem::class, mappedBy="lesson")
   */
  /**
   * @var Collection<int, OrderItem>
   */
  #[ORM\OneToMany(targetEntity: OrderItem::class, mappedBy: 'lesson')]
  private Collection $orderItems;

  /**
   * @var Collection<int, Completion> The completions of the lesson.
   * 
   * @ORM\OneToMany(targetEntity=Completion::class, mappedBy="lesson", orphanRemoval=true)
   */
  /**
   * @var Collection<int, Completion>
   */
  #[ORM\OneToMany(targetEntity: Completion::class, mappedBy: 'lesson', orphanRemoval: true)]
  private Collection $completions;

  /**
   * Constructor to initialize the collections for chapters, order items, and completions.
   */
  public function __construct()
  {
    $this->chapters = new ArrayCollection();
    $this->orderItems = new ArrayCollection();
    $this->completions = new ArrayCollection();
  }

  /**
   * Sets the creation date of the lesson before persisting it.
   * 
   * @ORM\PrePersist
   */
  #[ORM\PrePersist]
  public function setCreatedAtValue(): void
  {
    $this->created_at = new \DateTimeImmutable();
  }

  /**
   * Sets the updated date of the lesson before persisting or updating it.
   * 
   * @ORM\PrePersist
   * @ORM\PreUpdate
   */
  #[ORM\PrePersist]
  #[ORM\PreUpdate]
  public function setUpdatedAtValue(): void
  {
    $this->updated_at = new \DateTimeImmutable();
  }

  /**
   * Generates the slug based on the lesson title before persisting or updating it.
   * 
   * @ORM\PrePersist
   * @ORM\PreUpdate
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
   * Get the ID of the lesson.
   *
   * @return int|null
   */
  public function getId(): ?int
  {
    return $this->id;
  }

  /**
   * Get the title of the lesson.
   *
   * @return string|null
   */
  public function getTitle(): ?string
  {
    return $this->title;
  }

  /**
   * Set the title of the lesson.
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
   * String representation of the lesson.
   *
   * @return string
   */
  public function __toString(): string
  {
    return $this->title;
  }

  /**
   * Get the description of the lesson.
   *
   * @return string|null
   */
  public function getDescription(): ?string
  {
    return $this->description;
  }

  /**
   * Set the description of the lesson.
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
   * Get the price of the lesson.
   *
   * @return string|null
   */
  public function getPrice(): ?string
  {
    return $this->price;
  }

  /**
   * Set the price of the lesson.
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
   * Get the course associated with the lesson.
   *
   * @return Courses|null
   */
  public function getCourse(): ?Courses
  {
    return $this->course;
  }

  /**
   * Set the course associated with the lesson.
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
   * Get the slug of the lesson.
   *
   * @return string|null
   */
  public function getSlug(): ?string
  {
    return $this->slug;
  }

  /**
   * Set the slug of the lesson.
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
   * Get the creation date of the lesson.
   *
   * @return \DateTimeImmutable|null
   */
  public function getCreatedAt(): ?\DateTimeImmutable
  {
    return $this->created_at;
  }

  /**
   * Set the creation date of the lesson.
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
   * Get the last updated date of the lesson.
   *
   * @return \DateTimeImmutable|null
   */
  public function getUpdatedAt(): ?\DateTimeImmutable
  {
    return $this->updated_at;
  }


  /**
   * Set the last update date of the lesson.
   *
   * @param \DateTimeImmutable $updated_at
   * @return static
   */
  public function setUpdatedAt(\DateTimeImmutable $updated_at): static
  {
    $this->updated_at = $updated_at;

    return $this;
  }

  /**
   * Get all chapters associated with the lesson.
   *
   * @return Collection<int, Chapters>
   */
  public function getChapters(): Collection
  {
    return $this->chapters;
  }

  /**
   * Add a chapter to the lesson.
   *
   * @param Chapters $chapter
   * @return static
   */
  public function addChapter(Chapters $chapter): static
  {
    if (!$this->chapters->contains($chapter)) {
      $this->chapters[] = $chapter;
      $chapter->setLessonId($this);
    }

    return $this;
  }

  /**
   * Remove a chapter from the lesson.
   *
   * @param Chapters $chapter
   * @return static
   */
  public function removeChapter(Chapters $chapter): static
  {
    if ($this->chapters->removeElement($chapter)) {
      // set the owning side to null
      if ($chapter->getLessonId() === $this) {
        $chapter->setLessonId(null);
      }
    }

    return $this;
  }

  /**
   * Get the highlight status of the lesson.
   *
   * @return bool|null
   */
  public function getHighlight(): ?bool
  {
    return $this->highlight;
  }

  /**
   * Set the highlight status of the lesson.
   *
   * @param bool $highlight
   * @return static
   */
  public function setHighlight(bool $highlight): static
  {
    $this->highlight = $highlight;

    return $this;
  }

  /**
   * Get the image path associated with the lesson.
   *
   * @return string|null
   */
  public function getImage(): ?string
  {
    return $this->image;
  }

  /**
   * Set the image path associated with the lesson.
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
   * Get the badge associated with the lesson.
   *
   * @return Badges|null
   */
  public function getBadge(): ?Badges
  {
    return $this->badge;
  }

  /**
   * Set the badge associated with the lesson.
   *
   * @param Badges|null $badge
   * @return static
   */
  public function setBadge(?Badges $badge): static
  {
    $this->badge = $badge;

    return $this;
  }

  /**
   * Get all order items associated with the lesson.
   *
   * @return Collection<int, OrderItem>
   */
  public function getOrderItems(): Collection
  {
    return $this->orderItems;
  }

  /**
   * Add an order item to the lesson.
   *
   * @param OrderItem $orderItem
   * @return static
   */
  public function addOrderItem(OrderItem $orderItem): static
  {
    if (!$this->orderItems->contains($orderItem)) {
      $this->orderItems[] = $orderItem;
      $orderItem->setLesson($this);
    }

    return $this;
  }

  /**
   * Remove an order item from the lesson.
   *
   * @param OrderItem $orderItem
   * @return static
   */
  public function removeOrderItem(OrderItem $orderItem): static
  {
    if ($this->orderItems->removeElement($orderItem)) {
      // set the owning side to null
      if ($orderItem->getLesson() === $this) {
        $orderItem->setLesson(null);
      }
    }

    return $this;
  }

  /**
   * Get all completions associated with the lesson.
   *
   * @return Collection<int, Completion>
   */
  public function getCompletions(): Collection
  {
    return $this->completions;
  }

  /**
   * Add a completion record to the lesson.
   *
   * @param Completion $completion
   * @return static
   */
  public function addCompletion(Completion $completion): static
  {
    if (!$this->completions->contains($completion)) {
      $this->completions[] = $completion;
      $completion->setLesson($this);
    }

    return $this;
  }

  /**
   * Remove a completion record from the lesson.
   *
   * @param Completion $completion
   * @return static
   */
  public function removeCompletion(Completion $completion): static
  {
    if ($this->completions->removeElement($completion)) {
      // set the owning side to null
      if ($completion->getLesson() === $this) {
        $completion->setLesson(null);
      }
    }

    return $this;
  }
}
