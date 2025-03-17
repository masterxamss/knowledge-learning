<?php

namespace App\Entity;

use App\Repository\LessonsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\String\Slugger\AsciiSlugger;

#[ORM\Entity(repositoryClass: LessonsRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Lessons
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(length: 255)]
  private ?string $title = null;

  #[ORM\Column(type: 'text', nullable: true, options: ['columnDefinition' => 'LONGTEXT'])]
  private ?string $description = null;

  #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
  private ?string $price = null;

  #[ORM\Column(length: 255, nullable: true)]
  private ?string $video = null;

  #[ORM\ManyToOne(targetEntity: Courses::class, inversedBy: 'lessons')]
  #[ORM\JoinColumn(name: 'course_id', referencedColumnName: 'id', nullable: false)]
  private ?Courses $course = null;

  #[ORM\Column(length: 255)]
  private ?string $slug = null;

  #[ORM\Column]
  private ?\DateTimeImmutable $created_at = null;

  #[ORM\Column]
  private ?\DateTimeImmutable $updated_at = null;

  #[ORM\Column(length: 255, nullable: true)]
  private ?string $icon1 = null;

  #[ORM\Column(length: 255, nullable: true)]
  private ?string $icon2 = null;

  /**
   * @var Collection<int, Chapters>
   */
  #[ORM\OneToMany(targetEntity: Chapters::class, mappedBy: 'lessonId')]
  private Collection $chapters;

  #[ORM\Column(nullable: true)]
  private ?bool $highlight = null;

  #[ORM\Column(length: 255, nullable: true)]
  private ?string $image = null;

  #[ORM\ManyToOne(targetEntity: Badges::class, inversedBy: 'lessons')]
  #[ORM\JoinColumn(name: 'badge', referencedColumnName: 'id', nullable: true)]
  private ?Badges $badge = null;

  public function __construct()
  {
    $this->chapters = new ArrayCollection();
  }

  #[ORM\PrePersist]
  public function setCreatedAtValue(): void
  {
    $this->created_at = new \DateTimeImmutable();
  }

  #[ORM\PrePersist]
  #[ORM\PreUpdate]
  public function setUpdatedAtValue(): void
  {
    $this->updated_at = new \DateTimeImmutable();
  }

  #[ORM\PrePersist]
  #[ORM\PreUpdate]
  public function generateSlug(): void
  {
    if (!empty($this->title)) {
      $this->slug = (new AsciiSlugger())->slug($this->title)->lower();
    }
  }

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getTitle(): ?string
  {
    return $this->title;
  }

  public function setTitle(string $title): static
  {
    $this->title = $title;

    return $this;
  }

  public function __toString(): string
  {
    return $this->title;
  }

  public function getDescription(): ?string
  {
    return $this->description;
  }

  public function setDescription(string $description): static
  {
    $this->description = $description;

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

  public function getVideo(): ?string
  {
    return $this->video;
  }

  public function setVideo(?string $video): static
  {
    $this->video = $video;

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

  public function getSlug(): ?string
  {
    return $this->slug;
  }

  public function setSlug(string $slug): static
  {
    $this->slug = $slug;

    return $this;
  }

  public function getCreatedAt(): ?\DateTimeImmutable
  {
    return $this->created_at;
  }

  public function setCreatedAt(\DateTimeImmutable $created_at): static
  {
    $this->created_at = $created_at;

    return $this;
  }

  public function getUpdatedAt(): ?\DateTimeImmutable
  {
    return $this->updated_at;
  }

  public function setUpdatedAt(\DateTimeImmutable $updated_at): static
  {
    $this->updated_at = $updated_at;

    return $this;
  }

  public function getIcon1(): ?string
  {
    return $this->icon1;
  }

  public function setIcon1(?string $icon1): static
  {
    $this->icon1 = $icon1;

    return $this;
  }

  public function getIcon2(): ?string
  {
    return $this->icon2;
  }

  public function setIcon2(?string $icon2): static
  {
    $this->icon2 = $icon2;

    return $this;
  }

  /**
   * @return Collection<int, Chapters>
   */
  public function getChapters(): Collection
  {
    return $this->chapters;
  }

  public function addChapter(Chapters $chapter): static
  {
    if (!$this->chapters->contains($chapter)) {
      $this->chapters->add($chapter);
      $chapter->setLessonId($this);
    }

    return $this;
  }

  public function removeChapter(Chapters $chapter): static
  {
    if ($this->chapters->removeElement($chapter)) {
      // set the owning side to null (unless already changed)
      if ($chapter->getLessonId() === $this) {
        $chapter->setLessonId(null);
      }
    }

    return $this;
  }

  public function isHighlight(): ?bool
  {
    return $this->highlight;
  }

  public function setHighlight(?bool $highlight): static
  {
    $this->highlight = $highlight;

    return $this;
  }

  public function getImage(): ?string
  {
    return $this->image;
  }

  public function setImage(?string $image): static
  {
    $this->image = $image;

    return $this;
  }

  public function getBadge(): ?Badges
  {
    return $this->badge;
  }

  public function setBadge(?Badges $badge): static
  {
    $this->badge = $badge;

    return $this;
  }
}
