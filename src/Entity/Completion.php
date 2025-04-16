<?php

namespace App\Entity;

use App\Repository\CompletionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompletionRepository::class)]
class Completion
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'completions')]
  #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
  private ?User $user = null;

  #[ORM\ManyToOne(targetEntity: Lessons::class, inversedBy: 'completions')]
  #[ORM\JoinColumn(name: 'lesson_id', referencedColumnName: 'id', nullable: false)]
  private ?Lessons $lesson = null;

  #[ORM\Column(length: 45)]
  private ?string $status = null;

  #[ORM\Column(nullable: true)]
  private ?\DateTimeImmutable $completionDate = null;

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getUser(): ?User
  {
    return $this->user;
  }

  public function setUser(?User $user): static
  {
    $this->user = $user;

    return $this;
  }

  public function getLesson(): ?Lessons
  {
    return $this->lesson;
  }

  public function setLesson(?Lessons $lesson): static
  {
    $this->lesson = $lesson;

    return $this;
  }

  public function getStatus(): ?string
  {
    return $this->status;
  }

  public function setStatus(string $status): static
  {
    $this->status = $status;

    return $this;
  }

  public function getCompletionDate(): ?\DateTimeImmutable
  {
    return $this->completionDate;
  }

  public function setCompletionDate(?\DateTimeImmutable $completionDate): static
  {
    $this->completionDate = $completionDate;

    return $this;
  }
}
