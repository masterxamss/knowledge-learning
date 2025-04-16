<?php

namespace App\Entity;

use App\Repository\CoursesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\String\Slugger\AsciiSlugger;

#[ORM\Entity(repositoryClass: CoursesRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Courses
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(length: 255)]
  private ?string $title = null;

  #[ORM\Column(type: 'text', nullable: true, options: ['columnDefinition' => 'LONGTEXT'])]
  private ?string $description = null;

  #[ORM\Column(length: 255, nullable: true)]
  private ?string $image = null;

  #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
  private ?string $price = null;

  #[ORM\Column(nullable: true)]
  private ?\DateTimeImmutable $created_at = null;

  #[ORM\Column(nullable: true)]
  private ?\DateTimeImmutable $updated_at = null;

  #[ORM\ManyToOne(targetEntity: Themes::class, inversedBy: 'courses')]
  #[ORM\JoinColumn(name: 'theme_id', referencedColumnName: 'id', nullable: false)]
  private ?Themes $theme = null;

  #[ORM\Column(length: 255)]
  private ?string $slug = null;

  /**
   * @var Collection<int, Lessons>
   */
  #[ORM\OneToMany(targetEntity: Lessons::class, mappedBy: 'course')]
  private Collection $lessons;

  #[ORM\Column(nullable: true)]
  private ?bool $highlight = null;

  #[ORM\ManyToOne(targetEntity: Badges::class, inversedBy: 'courses')]
  #[ORM\JoinColumn(name: 'badge', referencedColumnName: 'id', nullable: true)]
  private ?Badges $badge = null;

  /**
   * @var Collection<int, OrderItem>
   */
  #[ORM\OneToMany(targetEntity: OrderItem::class, mappedBy: 'course')]
  private Collection $orderItems;

  /**
   * @var Collection<int, Certifications>
   */
  #[ORM\OneToMany(targetEntity: Certifications::class, mappedBy: 'course')]
  private Collection $certifications;

  public function __construct()
  {
    $this->lessons = new ArrayCollection();
    $this->orderItems = new ArrayCollection();
    $this->certifications = new ArrayCollection();
  }

  #[ORM\PrePersist]
  public function setCreatedAtValue(): void
  {
    $this->created_at = new \DateTimeImmutable();
  }

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

  public function getImage(): ?string
  {
    return $this->image;
  }

  public function setImage(?string $image): static
  {
    $this->image = $image;

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

  public function getCreatedAt(): ?\DateTimeImmutable
  {
    return $this->created_at;
  }

  public function setCreatedAt(?\DateTimeImmutable $created_at): static
  {
    $this->created_at = $created_at;

    return $this;
  }

  public function getUpdatedAt(): ?\DateTimeImmutable
  {
    return $this->updated_at;
  }

  public function setUpdatedAt(?\DateTimeImmutable $updated_at): static
  {
    $this->updated_at = $updated_at;

    return $this;
  }

  public function getTheme(): ?themes
  {
    return $this->theme;
  }

  public function setTheme(?themes $theme): static
  {
    $this->theme = $theme;

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

  /**
   * @return Collection<int, Lessons>
   */
  public function getLessons(): Collection
  {
    return $this->lessons;
  }

  public function addLesson(Lessons $lesson): static
  {
    if (!$this->lessons->contains($lesson)) {
      $this->lessons->add($lesson);
      $lesson->setCourse($this);
    }

    return $this;
  }

  public function removeLesson(Lessons $lesson): static
  {
    if ($this->lessons->removeElement($lesson)) {
      // set the owning side to null (unless already changed)
      if ($lesson->getCourse() === $this) {
        $lesson->setCourse(null);
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

  public function getBadge(): ?Badges
  {
    return $this->badge;
  }

  public function setBadge(?Badges $badge): static
  {
    $this->badge = $badge;

    return $this;
  }

  /**
   * @return Collection<int, OrderItem>
   */
  public function getOrderItems(): Collection
  {
      return $this->orderItems;
  }

  public function addOrderItem(OrderItem $orderItem): static
  {
      if (!$this->orderItems->contains($orderItem)) {
          $this->orderItems->add($orderItem);
          $orderItem->setCourse($this);
      }

      return $this;
  }

  public function removeOrderItem(OrderItem $orderItem): static
  {
      if ($this->orderItems->removeElement($orderItem)) {
          // set the owning side to null (unless already changed)
          if ($orderItem->getCourse() === $this) {
              $orderItem->setCourse(null);
          }
      }

      return $this;
  }

  /**
   * @return Collection<int, Certifications>
   */
  public function getCertifications(): Collection
  {
      return $this->certifications;
  }

  public function addCertification(Certifications $certification): static
  {
      if (!$this->certifications->contains($certification)) {
          $this->certifications->add($certification);
          $certification->setCourse($this);
      }

      return $this;
  }

  public function removeCertification(Certifications $certification): static
  {
      if ($this->certifications->removeElement($certification)) {
          // set the owning side to null (unless already changed)
          if ($certification->getCourse() === $this) {
              $certification->setCourse(null);
          }
      }

      return $this;
  }
}
