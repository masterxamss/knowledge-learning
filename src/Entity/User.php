<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Represents a user in the system.
 *
 * This class implements the UserInterface and PasswordAuthenticatedUserInterface
 * from Symfony Security, providing the necessary methods for user authentication
 * and authorization. It stores user details such as email, password, names,
 * address, verification status, activity status, creation and update timestamps,
 * profile image, title, description, social media links, orders, completions,
 * and certifications.
 *
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\UniqueConstraint(name="UNIQ_IDENTIFIER_EMAIL", fields={"email"})
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 * @ORM\HasLifecycleCallbacks
 */

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
#[ORM\HasLifecycleCallbacks]

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
  /**
   * @var int|null The unique identifier of the user
   */
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  /**
   * @var string|null The email address of the user, used for authentication
   * @Assert\NotBlank(message="constraints.not_blank")
   * @Assert\Email(message="constraints.email")
   */
  #[ORM\Column(type: 'string', length: 180, unique: true)]
  #[Assert\NotBlank(message: 'constraints.not_blank')]
  #[Assert\Email(message: 'constraints.email')]
  private ?string $email = null;

  /**
   * @var array The roles assigned to the user
   */
  #[ORM\Column]
  private array $roles = [];

  /**
   * @var string|null The hashed password of the user
   * @Assert\NotBlank(message="constraints.not_blank")
   * @Assert\Length(min=8, minMessage="constraints.min_length")
   * @Assert\Length(max=250, minMessage="constraints.max_length")
   * @Assert\Regex(pattern="/^(?=.*[A-Z])(?=.*[\W_])[A-Za-z\d\W_]{8,}$/", message="constraints.password")
   */
  #[ORM\Column(type: 'string')]
  #[Assert\NotBlank(message: 'constraints.not_blank')]
  #[Assert\Length(min: 8, minMessage: 'constraints.min_length')]
  #[Assert\Length(max: 250, minMessage: 'constraints.max_length')]
  #[Assert\Regex(pattern: '/^(?=.*[A-Z])(?=.*[\W_])[A-Za-z\d\W_]{8,}$/', message: 'constraints.password')]
  private ?string $password = null;

  /**
   * @var string|null The first name of the user
   * @Assert\NotBlank(message="constraints.not_blank")
   * @Assert\Length(min=2, minMessage="constraints.min_length")
   * @Assert\Length(max=250, minMessage="constraints.max_length")
   * @Assert\Regex(pattern="/^[a-zA-ZÀ-ÖØ-öø-ÿ\s]+$/", message="constraints.regex")
   */
  #[ORM\Column(type: 'string', length: 255)]
  #[Assert\NotBlank(message: 'constraints.not_blank')]
  #[Assert\Length(min: 2, minMessage: 'constraints.min_length')]
  #[Assert\Length(max: 250, minMessage: 'constraints.max_length')]
  #[Assert\Regex(pattern: '/^[a-zA-ZÀ-ÖØ-öø-ÿ\s]+$/', message: 'constraints.regex')]
  private ?string $first_name = null;

  /**
   * @var string|null The last name of the user
   * @Assert\NotBlank(message="constraints.not_blank")
   * @Assert\Length(min=2, minMessage="constraints.min_length")
   * @Assert\Length(max=250, minMessage="constraints.max_length")
   * @Assert\Regex(pattern="/^[a-zA-ZÀ-ÖØ-öø-ÿ\s]+$/", message="constraints.regex")
   */
  #[ORM\Column(type: 'string', length: 255)]
  #[Assert\NotBlank(message: 'constraints.not_blank')]
  #[Assert\Length(min: 2, minMessage: 'constraints.min_length')]
  #[Assert\Length(max: 250, minMessage: 'constraints.max_length')]
  #[Assert\Regex(pattern: '/^[a-zA-ZÀ-ÖØ-öø-ÿ\s]+$/', message: 'constraints.regex')]
  private ?string $last_name = null;

  /**
   * @var array The user's address, which includes street, complement, city, zipcode, state, and country
   */
  #[ORM\Column(nullable: false)]
  private array $address = [
    'street'      => null,
    'complement'  => null,
    'city'        => null,
    'zipcode'     => null,
    'state'       => null,
    'country'     => null,
  ];

  /**
   * @var bool|null Indicates whether the user's email is verified
   */
  #[ORM\Column]
  private ?bool $isVerified = false;

  /**
   * @var bool|null Indicates whether the user is active
   */
  #[ORM\Column]
  private ?bool $active = true;

  /**
   * @var \DateTimeImmutable|null The timestamp when the user was created
   */
  #[ORM\Column]
  private ?\DateTimeImmutable $created_at = null;

  /**
   * @var \DateTimeImmutable|null The timestamp when the user was last updated
   */
  #[ORM\Column]
  private ?\DateTimeImmutable $updated_at = null;

  /**
   * @var string|null The user's profile image
   */
  #[ORM\Column(length: 255, nullable: true)]
  private ?string $image = null;

  /**
   * @var string|null The user's title (e.g., Mr., Mrs., Dr.)
   */
  #[ORM\Column(type: 'string', length: 255, nullable: true)]
  private ?string $title = null;

  /**
   * @var string|null A description of the user
   */
  #[ORM\Column(type: 'text', nullable: true, options: ['columnDefinition' => 'LONGTEXT'])]
  private ?string $description = null;

  /**
   * @var array The user's social media links (e.g., GitHub, LinkedIn)
   */
  #[ORM\Column(type: 'json', nullable: false)]
  private array $links = [
    'github'    => null,
    'linkedin'  => null,
    'website'   => null,
    'twitter'   => null,
    'youtube'   => null,
    'facebook'  => null,
  ];

  /**
   * @var Collection<int, Order> The user's orders
   */
  #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'user', orphanRemoval: true)]
  private Collection $orders;

  /**
   * @var Collection<int, Completion> The user's course completions
   */
  #[ORM\OneToMany(targetEntity: Completion::class, mappedBy: 'user', orphanRemoval: true)]
  private Collection $completions;

  /**
   * @var Collection<int, Certifications> The user's certifications
   */
  #[ORM\OneToMany(targetEntity: Certifications::class, mappedBy: 'user', orphanRemoval: true)]
  private Collection $certifications;

  /**
   * Constructor initializes the collections for orders, completions, and certifications.
   */
  public function __construct()
  {
    $this->orders = new ArrayCollection();
    $this->completions = new ArrayCollection();
    $this->certifications = new ArrayCollection();
  }

  /**
   * Sets the creation timestamp before the entity is persisted.
   *
   * @return void
   */
  #[ORM\PrePersist]
  public function setCreatedAtValue(): void
  {
    $this->created_at = new \DateTimeImmutable();
  }

  /**
   * Sets the update timestamp before the entity is updated or persisted.
   *
   * @return void
   */
  #[ORM\PreUpdate]
  #[ORM\PrePersist]
  public function setUpdatedAtValue(): void
  {
    $this->updated_at = new \DateTimeImmutable();
  }

  /**
   * Get the unique identifier of the user.
   *
   * @return int|null
   */
  public function getId(): ?int
  {
    return $this->id;
  }

  /**
   * Get the email address of the user.
   *
   * @return string|null
   */
  public function getEmail(): ?string
  {
    return $this->email;
  }

  /**
   * Set the email address of the user.
   *
   * @param string $email
   * @return static
   */
  public function setEmail(string $email): static
  {
    $this->email = $email;

    return $this;
  }

  /**
   * A visual identifier that represents this user.
   *
   * @see UserInterface
   */
  public function getUserIdentifier(): string
  {
    return (string) $this->email;
  }

  /**
   * @see UserInterface
   *
   * @return array<string>
   */
  public function getRoles(): array
  {
    $roles = $this->roles;
    // guarantee every user at least has ROLE_USER
    $roles[] = 'ROLE_USER';

    return array_unique($roles);
  }

  /**
   * @param array<string> $roles
   * @return static
   */
  public function setRoles(array $roles): static
  {
    $this->roles = $roles;

    return $this;
  }

  /**
   * @see PasswordAuthenticatedUserInterface
   */
  public function getPassword(): ?string
  {
    return $this->password;
  }

  /**
   * Set the hashed password of the user.
   *
   * @param string $password
   * @return static
   */
  public function setPassword(string $password): static
  {
    $this->password = $password;

    return $this;
  }

  /**
   * @see UserInterface
   */
  public function eraseCredentials(): void
  {
    // If you store any temporary, sensitive data on the user, clear it here
    // $this->plainPassword = null;
  }

  /**
   * Get the first name of the user.
   *
   * @return string|null
   */
  public function getFirstName(): ?string
  {
    return $this->first_name;
  }

  /**
   * Set the first name of the user.
   *
   * @param string $first_name
   * @return static
   */
  public function setFirstName(string $first_name): static
  {
    $this->first_name = $first_name;

    return $this;
  }

  /**
   * Get the last name of the user.
   *
   * @return string|null
   */
  public function getLastName(): ?string
  {
    return $this->last_name;
  }

  /**
   * Set the last name of the user.
   *
   * @param string $last_name
   * @return static
   */
  public function setLastName(string $last_name): static
  {
    $this->last_name = $last_name;

    return $this;
  }

  /**
   * Get the full address of the user.
   *
   * @return array<string, string|null>
   */
  public function getAddress(): array
  {
    return array_merge([
      'street'     => null,
      'complement' => null,
      'city'       => null,
      'zipcode'    => null,
      'state'      => null,
      'country'    => null,
    ], $this->address);
  }

  /**
   * Set the address details of the user.
   *
   * @param array<string, string|null> $address
   * @return static
   */
  public function setAddress(array $address): static
  {
    $defaultAddress = [
      'street'     => null,
      'complement' => null,
      'city'       => null,
      'zipcode'    => null,
      'state'      => null,
      'country'    => null,
    ];

    $this->address = array_merge($defaultAddress, $address ?? []);

    return $this;
  }

  /**
   * Get a specific part of the user's address.
   *
   * @param string $key The key of the address part to retrieve (e.g., 'street', 'city').
   * @return string|null
   */
  public function getAddressForKey(string $key): ?string
  {
    return $this->address[$key] ?? null;
  }

  /**
   * Set a specific part of the user's address.
   *
   * @param string $key The key of the address part to set.
   * @param string|null $value The value to set for the address part.
   * @return static
   */
  public function setAddressForKey(string $key, ?string $value): static
  {
    $key = $this->validKey($key);

    $this->address[$key] = $value;

    return $this;
  }

  /**
   * Check if the user's email is verified.
   *
   * @return bool|null
   */
  public function getIsVerified(): ?bool
  {
    return $this->isVerified;
  }

  /**
   * Set whether the user's email is verified.
   *
   * @param bool $isVerified
   * @return static
   */
  public function setIsVerified(bool $isVerified): static
  {
    $this->isVerified = $isVerified;

    return $this;
  }

  /**
   * Get the activity status of the user.
   *
   * @return bool|null
   */
  public function getActive(): ?bool
  {
    return $this->active;
  }

  /**
   * Set the activity status of the user.
   *
   * @param bool $active
   * @return static
   */
  public function setActive(bool $active): static
  {
    $this->active = $active;

    return $this;
  }

  /**
   * Get the creation date of the user account.
   *
   * @return \DateTimeImmutable|null
   */
  public function getCreatedAt(): ?\DateTimeImmutable
  {
    return $this->created_at;
  }

  /**
   * Set the creation date of the user account.
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
   * Get the last update date of the user account.
   *
   * @return \DateTimeImmutable|null
   */
  public function getUpdatedAt(): ?\DateTimeImmutable
  {
    return $this->updated_at;
  }

  /**
   * Set the last update date of the user account.
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
   * Get the filename or path of the user's profile image.
   *
   * @return string|null
   */
  public function getImage(): ?string
  {
    return $this->image;
  }

  /**
   * Set the filename or path of the user's profile image.
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
   * Get the title or profession of the user.
   *
   * @return string|null
   */
  public function getTitle(): ?string
  {
    return $this->title;
  }

  /**
   * Set the title or profession of the user.
   *
   * @param string|null $title
   * @return static
   */
  public function setTitle(?string $title): static
  {
    $this->title = $title;

    return $this;
  }

  /**
   * Get the description or bio of the user.
   *
   * @return string|null
   */
  public function getDescription(): ?string
  {
    return $this->description;
  }

  /**
   * Set the description or bio of the user.
   *
   * @param string|null $description
   * @return static
   */
  public function setDescription(?string $description): static
  {
    $this->description = $description;

    return $this;
  }

  /**
   * Get the social media and website links of the user.
   *
   * @return array<string, string|null>
   */
  public function getLinks(): array
  {
    return array_merge([
      'github'   => null,
      'linkedin' => null,
      'website'  => null,
      'twitter'  => null,
      'youtube'  => null,
      'facebook' => null,
    ], $this->links);
  }

  /**
   * Set the social media and website links of the user.
   *
   * @param array<string, string|null>|null $links
   * @return static
   */
  public function setLinks(?array $links): static
  {
    $defaultLinks = [
      'github'   => null,
      'linkedin' => null,
      'website'  => null,
      'twitter'  => null,
      'youtube'  => null,
      'facebook' => null,
    ];

    $this->links = array_merge($defaultLinks, $links ?? []);

    return $this;
  }

  /**
   * Get a specific social media or website link of the user.
   *
   * @param string $key The key of the link to retrieve (e.g., 'github', 'twitter').
   * @return string|null
   */
  public function getLinkForKey(string $key): ?string
  {
    return $this->links[$key] ?? null;
  }

  /**
   * Set a specific social media or website link of the user.
   *
   * @param string $key The key of the link to set.
   * @param string|null $value The value to set for the link.
   * @return static
   */
  public function setLinkForKey(string $key, ?string $value): static
  {
    $this->validKey($key);

    $this->links[$key] = $value;

    return $this;
  }

  /**
   * Validates if the provided key is valid, belonging to either the 'links' or 'address' groups.
   *
   * This function checks if the provided key exists in the predefined keys for 'links' or 'address'.
   * If the key is valid, it is returned. Otherwise, an `InvalidArgumentException` is thrown.
   *
   * @param string $key The key to be validated. It should be one of the predefined keys in either the 'links' or 'address' arrays.
   *
   * @return string Returns the provided key if it is valid.
   *
   * @throws \InvalidArgumentException If the provided key is not valid.
   */
  private function validKey(string $key): string
  {
    $validKeys = [
      'links' => [
        'github',
        'linkedin',
        'website',
        'twitter',
        'youtube',
        'facebook'
      ],
      'address' => [
        'street',
        'complement',
        'city',
        'zipcode',
        'state',
        'country'
      ]
    ];

    // Loop through each category (links and address)
    foreach ($validKeys as $category => $keys) {
      foreach ($keys as $validKey) {
        if ($key === $validKey) {
          return $key; // Return the key if it's valid
        }
      }
    }

    // If the key isn't found, throw an exception
    throw new \InvalidArgumentException('Invalid key');
  }

  /**
   * Checks if the user's email is verified.
   *
   * @return bool
   */
  public function isVerified(): bool
  {
    return $this->isVerified;
  }

  /**
   * Get the orders placed by the user.
   *
   * @return Collection<int, Order>
   */
  public function getOrders(): Collection
  {
    return $this->orders;
  }

  /**
   * Add an order to the user.
   *
   * @param Order $order
   * @return static
   */
  public function addOrder(Order $order): static
  {
    if (!$this->orders->contains($order)) {
      $this->orders->add($order);
      $order->setUser($this);
    }

    return $this;
  }

  /**
   * Remove an order from the user.
   *
   * @param Order $order
   * @return static
   */
  public function removeOrder(Order $order): static
  {
    if ($this->orders->removeElement($order)) {
      // set the owning side to null (unless already changed)
      if ($order->getUser() === $this) {
        $order->setUser(null);
      }
    }

    return $this;
  }

  /**
   * Get the course completions recorded for the user.
   *
   * @return Collection<int, Completion>
   */
  public function getCompletions(): Collection
  {
    return $this->completions;
  }

  /**
   * Add a course completion for the user.
   *
   * @param Completion $completion
   * @return static
   */
  public function addCompletion(Completion $completion): static
  {
    if (!$this->completions->contains($completion)) {
      $this->completions->add($completion);
      $completion->setUser($this);
    }

    return $this;
  }

  /**
   * Remove a course completion from the user.
   *
   * @param Completion $completion
   * @return static
   */
  public function removeCompletion(Completion $completion): static
  {
    if ($this->completions->removeElement($completion)) {
      // set the owning side to null (unless already changed)
      if ($completion->getUser() === $this) {
        $completion->setUser(null);
      }
    }

    return $this;
  }

  /**
   * Get the certifications earned by the user.
   *
   * @return Collection<int, Certifications>
   */
  public function getCertifications(): Collection
  {
    return $this->certifications;
  }

  /**
   * Add a certification for the user.
   *
   * @param Certifications $certification
   * @return static
   */
  public function addCertification(Certifications $certification): static
  {
    if (!$this->certifications->contains($certification)) {
      $this->certifications->add($certification);
      $certification->setUser($this);
    }

    return $this;
  }

  /**
   * Remove a certification from the user.
   *
   * @param Certifications $certification
   * @return static
   */
  public function removeCertification(Certifications $certification): static
  {
    if ($this->certifications->removeElement($certification)) {
      // set the owning side to null (unless already changed)
      if ($certification->getUser() === $this) {
        $certification->setUser(null);
      }
    }

    return $this;
  }

  /**
   * Configures the default options for a User form.
   *
   * @param OptionsResolver $resolver The resolver for the options.
   * @return void
   */
  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'data_class' => User::class,
      'translation_domain' => 'forms',
    ]);
  }
}
