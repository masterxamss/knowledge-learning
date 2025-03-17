<?php

namespace App\Entity;

use App\Repository\ThemesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ThemesRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Themes
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(length: 255)]
  private ?string $name = null;

  #[ORM\Column(type: 'text', nullable: true, options: ['columnDefinition' => 'LONGTEXT'])]
  private ?string $description = null;

  #[ORM\Column(length: 255, nullable: true)]
  private ?string $image = null;

  #[ORM\Column]
  private ?\DateTimeImmutable $created_at = null;

  #[ORM\Column(length: 255)]
  private ?string $slug = null;

  #[ORM\Column(length: 255)]
  private ?string $title = null;

  /**
   * @var Collection<int, Courses>
   */
  #[ORM\OneToMany(targetEntity: Courses::class, mappedBy: 'theme')]
  private Collection $courses;

  #[ORM\Column(nullable: true)]
  private ?bool $highlight = null;

  #[ORM\PrePersist]
  #[ORM\PreUpdate]
  public function generateSlug(): void
  {
    if (!empty($this->name)) {
      $this->slug = (new AsciiSlugger())->slug($this->name)->lower();
    }
  }

  public function __construct()
  {
    $this->courses = new ArrayCollection();
    $this->created_at = new \DateTimeImmutable();
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

  public function getCreatedAt(): ?\DateTimeImmutable
  {
    return $this->created_at;
  }

  public function setCreatedAt(\DateTimeImmutable $created_at): static
  {
    $this->created_at = $created_at;

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

  public function getTitle(): ?string
  {
    return $this->title;
  }

  public function setTitle(string $title): static
  {
    $this->title = $title;

    return $this;
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
      $course->setTheme($this);
    }

    return $this;
  }

  public function removeCourse(Courses $course): static
  {
    if ($this->courses->removeElement($course)) {
      // set the owning side to null (unless already changed)
      if ($course->getTheme() === $this) {
        $course->setTheme(null);
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
}
