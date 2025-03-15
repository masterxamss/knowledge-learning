<?php

namespace App\Entity;

use App\Repository\ChaptersRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\String\Slugger\AsciiSlugger;

#[ORM\Entity(repositoryClass: ChaptersRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Chapters
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(length: 255)]
  private ?string $title = null;

  #[ORM\Column(type: 'text', nullable: false, options: ['columnDefinition' => 'LONGTEXT'])]
  private ?string $content = null;

  #[ORM\ManyToOne(targetEntity: Lessons::class, inversedBy: 'chapters')]
  #[ORM\JoinColumn(name: 'lesson_id', referencedColumnName: 'id', nullable: false)]
  private ?Lessons $lessonId = null;

  #[ORM\Column]
  private ?\DateTimeImmutable $updatedAt = null;

  #[ORM\Column]
  private ?\DateTimeImmutable $createdAt = null;

  #[ORM\Column(length: 255, nullable: true)]
  private ?string $image = null;

  #[ORM\Column(length: 255)]
  private ?string $slug = null;

  #[ORM\PrePersist]
  public function setCreatedAtValue(): void
  {
    $this->createdAt = new \DateTimeImmutable();
  }

  #[ORM\PrePersist]
  #[ORM\PreUpdate]
  public function setUpdatedAtValue(): void
  {
    $this->updatedAt = new \DateTimeImmutable();
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

  public function getContent(): ?string
  {
    return $this->content;
  }

  public function setContent(string $content): static
  {
    $this->content = $content;

    return $this;
  }

  public function getLessonId(): ?Lessons
  {
    return $this->lessonId;
  }

  public function setLessonId(?Lessons $lessonId): static
  {
    $this->lessonId = $lessonId;

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

  public function getCreatedAt(): ?\DateTimeImmutable
  {
    return $this->createdAt;
  }

  public function setCreatedAt(\DateTimeImmutable $createdAt): static
  {
    $this->createdAt = $createdAt;

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

  public function getSlug(): ?string
  {
    return $this->slug;
  }

  public function setSlug(string $slug): static
  {
    $this->slug = $slug;

    return $this;
  }
}
