<?php

namespace App\Entity;

use App\Repository\CompletionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Completion represents the tracking of a user's progress in a specific lesson.
 * It stores information such as the user who completed the lesson, the lesson itself,
 * the status of the completion (e.g., "completed" or "in progress"), and the date of completion.
 * This entity is linked to the User and Lessons entities, capturing which lesson has been completed
 * by a particular user and the associated completion status.
 * 
 * @ORM\Entity(repositoryClass=CompletionRepository::class)
 */
#[ORM\Entity(repositoryClass: CompletionRepository::class)]
class Completion
{
  /**
   * The unique identifier for the completion record.
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
   * The user who completed the lesson.
   * 
   * @var User|null
   * 
   * @ORM\ManyToOne(targetEntity=User::class, inversedBy="completions")
   * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
   */
  #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'completions')]
  #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
  private ?User $user = null;

  /**
   * The lesson that was completed.
   * 
   * @var Lessons|null
   * 
   * @ORM\ManyToOne(targetEntity=Lessons::class, inversedBy="completions")
   * @ORM\JoinColumn(name="lesson_id", referencedColumnName="id", nullable=false)
   */
  #[ORM\ManyToOne(targetEntity: Lessons::class, inversedBy: 'completions')]
  #[ORM\JoinColumn(name: 'lesson_id', referencedColumnName: 'id', nullable: false)]
  private ?Lessons $lesson = null;


  /**
   * The status of the completion (e.g., "completed", "in progress").
   * 
   * @var string|null
   * 
   * @ORM\Column(length=45)
   */
  #[ORM\Column(length: 45)]
  private ?string $status = null;

  /**
   * The date when the completion was recorded.
   * 
   * @var \DateTimeImmutable|null
   * 
   * @ORM\Column(nullable=true)
   */
  #[ORM\Column(nullable: true)]
  private ?\DateTimeImmutable $completionDate = null;

  /**
   * Gets the ID of the completion.
   * 
   * @return int|null The ID of the completion.
   */  public function getId(): ?int
  {
    return $this->id;
  }

  /**
   * Gets the user associated with the completion.
   * 
   * @return User|null The user who completed the lesson.
   */
  public function getUser(): ?User
  {
    return $this->user;
  }

  /**
   * Sets the user for the completion.
   * 
   * @param User|null $user The user who completed the lesson.
   * 
   * @return static The current instance of the class.
   */
  public function setUser(?User $user): static
  {
    $this->user = $user;

    return $this;
  }

  /**
   * Gets the lesson associated with the completion.
   * 
   * @return Lessons|null The lesson that was completed.
   */
  public function getLesson(): ?Lessons
  {
    return $this->lesson;
  }

  /**
   * Sets the lesson for the completion.
   * 
   * @param Lessons|null $lesson The lesson that was completed.
   * 
   * @return static The current instance of the class.
   */
  public function setLesson(?Lessons $lesson): static
  {
    $this->lesson = $lesson;

    return $this;
  }

  /**
   * Gets the status of the completion.
   * 
   * @return string|null The status of the completion (e.g., "completed", "in progress").
   */
  public function getStatus(): ?string
  {
    return $this->status;
  }

  /**
   * Sets the status of the completion.
   * 
   * @param string $status The status of the completion (e.g., "completed", "in progress").
   * 
   * @return static The current instance of the class.
   */
  public function setStatus(string $status): static
  {
    $this->status = $status;

    return $this;
  }

  /**
   * Gets the completion date.
   * 
   * @return \DateTimeImmutable|null The date when the completion was recorded.
   */
  public function getCompletionDate(): ?\DateTimeImmutable
  {
    return $this->completionDate;
  }

  /**
   * Sets the completion date.
   * 
   * @param \DateTimeImmutable|null $completionDate The date when the completion was recorded.
   * 
   * @return static The current instance of the class.
   */
  public function setCompletionDate(?\DateTimeImmutable $completionDate): static
  {
    $this->completionDate = $completionDate;

    return $this;
  }
}
