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

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
#[ORM\HasLifecycleCallbacks]

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(type: 'string', length: 180, unique: true)]
  #[Assert\NotBlank(message: 'constraints.not_blank')]
  #[Assert\Email(message: 'constraints.email')]
  private ?string $email = null;

  /**
   * @var list<string> The user roles
   */
  #[ORM\Column]
  private array $roles = [];
  #Tiago_1987
  /**
   * @var string The hashed password
   */
  #[ORM\Column(type: 'string')]
  #[Assert\NotBlank(message: 'constraints.not_blank')]
  #[Assert\Length(min: 8, minMessage: 'constraints.min_length')]
  #[Assert\Length(max: 250, minMessage: 'constraints.max_length')]
  #[Assert\Regex(pattern: '/^(?=.*[A-Z])(?=.*[\W_])[A-Za-z\d\W_]{8,}$/
', message: 'constraints.password')]
  private ?string $password = null;

  #[ORM\Column(type: 'string', length: 255)]
  #[Assert\NotBlank(message: 'constraints.not_blank')]
  #[Assert\Length(min: 2, minMessage: 'constraints.min_length')]
  #[Assert\Length(max: 250, minMessage: 'constraints.max_length')]
  #[Assert\Regex(pattern: '/^[a-zA-ZÀ-ÖØ-öø-ÿ\s]+$/', message: 'constraints.regex')]
  private ?string $first_name = null;

  #[ORM\Column(type: 'string', length: 255)]
  #[Assert\NotBlank(message: 'constraints.not_blank')]
  #[Assert\Length(min: 2, minMessage: 'constraints.min_length')]
  #[Assert\Length(max: 250, minMessage: 'constraints.max_length')]
  #[Assert\Regex(pattern: '/^[a-zA-ZÀ-ÖØ-öø-ÿ\s]+$/', message: 'constraints.regex')]
  private ?string $last_name = null;

  #[ORM\Column(nullable: false)]
  private array $address = [
    'street'      => null,
    'complement'  => null,
    'city'        => null,
    'zipcode'     => null,
    'state'       => null,
    'country'     => null,
  ];

  #[ORM\Column]
  private ?bool $isVerified = false;

  #[ORM\Column]
  private ?bool $active = true;

  #[ORM\Column]
  private ?\DateTimeImmutable $created_at = null;

  #[ORM\Column]
  private ?\DateTimeImmutable $updated_at = null;

  #[ORM\Column(length: 255, nullable: true)]
  private ?string $image = null;

  #[ORM\Column(type: 'string', length: 255, nullable: true)]
  private ?string $title = null;

  #[ORM\Column(type: 'text', nullable: true, options: ['columnDefinition' => 'LONGTEXT'])]
  private ?string $description = null;

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
   * @var Collection<int, Order>
   */
  #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'user', orphanRemoval: true)]
  private Collection $orders;

  /**
   * @var Collection<int, Completion>
   */
  #[ORM\OneToMany(targetEntity: Completion::class, mappedBy: 'user', orphanRemoval: true)]
  private Collection $completions;

  /**
   * @var Collection<int, Certifications>
   */
  #[ORM\OneToMany(targetEntity: Certifications::class, mappedBy: 'user', orphanRemoval: true)]
  private Collection $certifications;

  public function __construct()
  {
    $this->orders = new ArrayCollection();
    $this->completions = new ArrayCollection();
    $this->certifications = new ArrayCollection();
  }

  #[ORM\PrePersist]
  public function setCreatedAtValue(): void
  {
    $this->created_at = new \DateTimeImmutable();
  }

  #[ORM\PreUpdate]
  #[ORM\PrePersist]
  public function setUpdatedAtValue(): void
  {
    $this->updated_at = new \DateTimeImmutable();
  }

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getEmail(): ?string
  {
    return $this->email;
  }

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
   * @return list<string>
   */
  public function getRoles(): array
  {
    $roles = $this->roles;
    // guarantee every user at least has ROLE_USER
    $roles[] = 'ROLE_USER';

    return array_unique($roles);
  }

  /**
   * @param list<string> $roles
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

  public function getFirstName(): ?string
  {
    return $this->first_name;
  }

  public function setFirstName(string $first_name): static
  {
    $this->first_name = $first_name;

    return $this;
  }

  public function getLastName(): ?string
  {
    return $this->last_name;
  }

  public function setLastName(string $last_name): static
  {
    $this->last_name = $last_name;

    return $this;
  }

  public function getAddress(): array
  {
    return array_merge([
      'street'      => null,
      'complement'  => null,
      'city'        => null,
      'zipcode'     => null,
      'state'       => null,
      'country'     => null,
    ], $this->address);
  }

  public function setAddress(array $address): static
  {

    $defaultAddress = [
      'street'      => null,
      'complement'  => null,
      'city'        => null,
      'zipcode'     => null,
      'state'       => null,
      'country'     => null,
    ];


    $this->address = array_merge($defaultAddress, $address ?? []);

    return $this;
  }


  public function getAddressForKey(string $key): ?string
  {
    return $this->address[$key] ?? null;
  }

  public function setAddressForKey(string $key, ?string $value): static
  {
    $key = $this->validKey($key);

    $this->address[$key] = $value;

    return $this;
  }

  public function getIsVerified(): ?bool
  {
    return $this->isVerified;
  }

  public function setIsVerified(bool $isVerified): static
  {
    $this->isVerified = $isVerified;

    return $this;
  }

  /*public function getActivationToken(): ?string
  {
    return $this->activationToken;
  }

  public function setActivationToken(?string $activationToken): static
  {
    $this->activationToken = $activationToken;

    return $this;
  }*/

  public function getActive(): ?bool
  {
    return $this->active;
  }

  public function setActive(bool $active): static
  {
    $this->active = $active;

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

  public function getImage(): ?string
  {
    return $this->image;
  }

  public function setImage(?string $image): static
  {
    $this->image = $image;

    return $this;
  }

  public function getTitle(): ?string
  {
    return $this->title;
  }

  public function setTitle(?string $title): static
  {
    $this->title = $title;

    return $this;
  }

  public function getDescription(): ?string
  {
    return $this->description;
  }

  public function setDescription(?string $description): static
  {
    $this->description = $description;

    return $this;
  }

  public function getLinks(): array
  {
    return array_merge([
      'github'    => null,
      'linkedin'  => null,
      'website'   => null,
      'twitter'   => null,
      'youtube'   => null,
      'facebook'  => null,
    ], $this->links);
  }

  public function setLinks(?array $links): static
  {

    $defaultLinks = [
      'github'    => null,
      'linkedin'  => null,
      'website'   => null,
      'twitter'   => null,
      'youtube'   => null,
      'facebook'  => null,
    ];

    $this->links = array_merge($defaultLinks, $links ?? []);

    return $this;
  }

  public function getLinkForKey(string $key): ?string
  {
    return $this->links[$key] ?? null;
  }

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

  public function isVerified(): bool
  {
    return $this->isVerified;
  }

  /**
   * @return Collection<int, Order>
   */
  public function getOrders(): Collection
  {
    return $this->orders;
  }

  public function addOrder(Order $order): static
  {
    if (!$this->orders->contains($order)) {
      $this->orders->add($order);
      $order->setUser($this);
    }

    return $this;
  }

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
   * @return Collection<int, Completion>
   */
  public function getCompletions(): Collection
  {
    return $this->completions;
  }

  public function addCompletion(Completion $completion): static
  {
    if (!$this->completions->contains($completion)) {
      $this->completions->add($completion);
      $completion->setUser($this);
    }

    return $this;
  }

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
      $certification->setUser($this);
    }

    return $this;
  }

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

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'data_class' => User::class,
      'translation_domain' => 'forms',
    ]);
  }
}
