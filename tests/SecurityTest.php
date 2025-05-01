<?php

namespace App\Tests;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class UserRepositoryTest
 *
 * This test class performs functional and security validation
 * of the UserRepository, which manages data access for User entities.
 *
 * The following scenarios are tested:
 * 
 * 1. Verifying that a user can be retrieved by email.
 * 2. Ensuring that regular users do not have administrative roles.
 */
class SecurityTest extends KernelTestCase
{
  /**
   * @var \Doctrine\ORM\EntityManagerInterface
   * Entity manager used for interacting with the database.
   */
  private $entityManager;

  /**
   * Set up the test environment.
   * Boots the Symfony kernel and initializes a test user if not present.
   *
   * @return void
   */
  protected function setUp(): void
  {
    self::bootKernel();
    $this->entityManager = static::getContainer()->get('doctrine.orm.entity_manager');

    if (!$this->entityManager->getRepository(User::class)->findOneBy(['email' => 'TmDg1@example.com'])) {
      $user = new User();
      $user->setEmail('TmDg1@example.com');
      $user->setRoles(['ROLE_USER']);
      $user->setPassword(password_hash('password', PASSWORD_BCRYPT));
      $user->setFirstName('John');
      $user->setLastName('Doe');
      $user->setAddress([
        'street'     => '123 Main St',
        'complement' => 'Apt 4B',
        'city'       => 'San Francisco',
        'zipcode'    => '94105',
        'state'      => 'CA',
        'country'    => 'United States',
      ]);
      $user->setIsVerified(true);
      $user->setActive(true);
      $user->setCreatedAt(new \DateTimeImmutable());
      $user->setUpdatedAt(new \DateTimeImmutable());
      $user->setLinks([
        'github'   => 'https://github.com/johndoe',
        'linkedin' => 'https://linkedin.com/johndoe',
        'website'  => 'https://johndoe.com',
        'twitter'  => 'https://twitter.com/johndoe',
        'youtube'  => 'https://youtube.com/johndoe',
        'facebook' => 'https://facebook.com/johndoe',
      ]);

      $this->entityManager->persist($user);
      $this->entityManager->flush();
    }
  }

  /**
   * Functional test: Verify a user can be found by their email address.
   *
   * @return void
   */
  public function testFindUserByEmail(): void
  {
    $user = $this->entityManager
      ->getRepository(User::class)
      ->findOneBy(['email' => 'TmDg1@example.com']);

    $this->assertNotNull($user);
    $this->assertEquals('TmDg1@example.com', $user->getEmail());
  }

  /**
   * Security test: Ensure that regular users do not have the admin role.
   *
   * This test confirms that the user does not accidentally receive elevated privileges.
   *
   * @return void
   */
  public function testUserDoesNotHaveAdminRole(): void
  {
    $user = $this->entityManager
      ->getRepository(User::class)
      ->findOneBy(['email' => 'TmDg1@example.com']);

    $this->assertNotContains('ROLE_ADMIN', $user->getRoles());
  }
}
