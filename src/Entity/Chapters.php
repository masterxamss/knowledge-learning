<?php

namespace App\Entity;

use App\Repository\ChaptersRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\String\Slugger\AsciiSlugger;

/**
 * Represents a chapter within a lesson.
 *
 * A chapter is a section of a lesson that contains content, optional image, and optional video.
 * It automatically generates a slug based on the title for URL-friendly usage.
 *
 */
#[ORM\Entity(repositoryClass: ChaptersRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Chapters
{
  /**
   * The unique identifier of the chapter.
   *
   * @var int|null
   */
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  /**
   * The title of the chapter.
   *
   * @var string|null
   */
  #[ORM\Column(length: 255)]
  private ?string $title = null;

  /**
   * The textual content of the chapter.
   *
   * @var string|null
   */
  #[ORM\Column(type: 'text', nullable: false, options: ['columnDefinition' => 'LONGTEXT'])]
  private ?string $content = null;

  /**
   * The lesson to which this chapter belongs.
   *
   * @var Lessons|null
   */
  #[ORM\ManyToOne(targetEntity: Lessons::class, inversedBy: 'chapters')]
  #[ORM\JoinColumn(name: 'lesson_id', referencedColumnName: 'id', nullable: false)]
  private ?Lessons $lessonId = null;

  /**
   * The date and time when the chapter was last updated.
   *
   * @var \DateTimeImmutable|null
   */
  #[ORM\Column]
  private ?\DateTimeImmutable $updatedAt = null;

  /**
   * The date and time when the chapter was created.
   *
   * @var \DateTimeImmutable|null
   */
  #[ORM\Column]
  private ?\DateTimeImmutable $createdAt = null;

  /**
   * Optional image associated with the chapter.
   *
   * @var string|null
   */
  #[ORM\Column(length: 255, nullable: true)]
  private ?string $image = null;

  /**
   * URL-friendly version of the chapter title.
   *
   * @var string|null
   */
  #[ORM\Column(length: 255)]
  private ?string $slug = null;

  /**
   * Optional video associated with the chapter.
   *
   * @var string|null
   */
  #[ORM\Column(length: 255, nullable: true)]
  private ?string $video = null;

  /**
   * Sets the creation date automatically when the chapter is first persisted.
   *
   * @return void
   */
  #[ORM\PrePersist]
  public function setCreatedAtValue(): void
  {
    $this->createdAt = new \DateTimeImmutable();
  }

  /**
   * Sets the update date automatically when the chapter is persisted or updated.
   *
   * @return void
   */
  #[ORM\PrePersist]
  #[ORM\PreUpdate]
  public function setUpdatedAtValue(): void
  {
    $this->updatedAt = new \DateTimeImmutable();
  }

  /**
   * Generates the slug from the chapter title on persist or update.
   *
   * @return void
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
   * Gets the ID of the chapter.
   *
   * @return int|null
   */
  public function getId(): ?int
  {
    return $this->id;
  }

  /**
   * Gets the title of the chapter.
   *
   * @return string|null
   */
  public function getTitle(): ?string
  {
    return $this->title;
  }

  /**
   * Sets the title of the chapter.
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
   * Gets the content of the chapter.
   *
   * @return string|null
   */
  public function getContent(): ?string
  {
    return $this->content;
  }

  /**
   * Sets the content of the chapter.
   *
   * @param string $content
   * @return static
   */
  public function setContent(string $content): static
  {
    $this->content = $content;
    return $this;
  }

  /**
   * Gets the lesson associated with this chapter.
   *
   * @return Lessons|null
   */
  public function getLessonId(): ?Lessons
  {
    return $this->lessonId;
  }

  /**
   * Sets the lesson associated with this chapter.
   *
   * @param Lessons|null $lessonId
   * @return static
   */
  public function setLessonId(?Lessons $lessonId): static
  {
    $this->lessonId = $lessonId;
    return $this;
  }

  /**
   * Gets the update timestamp.
   *
   * @return \DateTimeImmutable|null
   */
  public function getUpdatedAt(): ?\DateTimeImmutable
  {
    return $this->updatedAt;
  }

  /**
   * Sets the update timestamp.
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
   * Gets the creation timestamp.
   *
   * @return \DateTimeImmutable|null
   */
  public function getCreatedAt(): ?\DateTimeImmutable
  {
    return $this->createdAt;
  }

  /**
   * Sets the creation timestamp.
   *
   * @param \DateTimeImmutable $createdAt
   * @return static
   */
  public function setCreatedAt(\DateTimeImmutable $createdAt): static
  {
    $this->createdAt = $createdAt;
    return $this;
  }

  /**
   * Gets the image associated with the chapter.
   *
   * @return string|null
   */
  public function getImage(): ?string
  {
    return $this->image;
  }

  /**
   * Sets the image associated with the chapter.
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
   * Gets the slug of the chapter.
   *
   * @return string|null
   */
  public function getSlug(): ?string
  {
    return $this->slug;
  }

  /**
   * Sets the slug of the chapter.
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
   * Gets the video associated with the chapter.
   *
   * @return string|null
   */
  public function getVideo(): ?string
  {
    return $this->video;
  }

  /**
   * Sets the video associated with the chapter.
   *
   * @param string|null $video
   * @return static
   */
  public function setVideo(?string $video): static
  {
    $this->video = $video;
    return $this;
  }
}
