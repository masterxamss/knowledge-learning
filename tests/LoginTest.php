<?php

namespace App\Tests;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class LoginTest
 *
 * This test class verifies the login functionality of the application.
 * It ensures that a user with valid credentials can authenticate
 * successfully via the login form.
 *
 * A test user is created during the setup phase if one does not already
 * exist in the database.
 *
 * 
 */
class LoginTest extends WebTestCase
{
  /**
   * @var \Symfony\Bundle\FrameworkBundle\KernelBrowser
   * HTTP client used to simulate browser interactions.
   */
  private $client;

  /**
   * @var \Doctrine\ORM\EntityManagerInterface
   * Doctrine entity manager used to interact with the database.
   */
  private $entityManager;

  /**
   * Initializes the test environment before each test.
   *
   * This method boots the Symfony kernel, creates the HTTP client,
   * retrieves the Doctrine entity manager, and ensures that a
   * test user exists in the database.
   *
   * @return void
   */
  protected function setUp(): void
  {
    $this->client = static::createClient();
    $this->entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');

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
   * Functional test: Attempt login with valid user credentials.
   *
   * This test simulates accessing the login page and submitting the
   * login form with valid credentials. It verifies that the response
   * is a redirect, indicating that the authentication was successful.
   *
   * @return void
   */
  public function testLoginWithValidCredentials(): void
  {
    $crawler = $this->client->request('GET', '/login');
    $this->assertResponseIsSuccessful();

    $this->client->submitForm('Sign in', [
      'email'    => 'TmDg1@example.com',
      'password' => 'password',
    ]);

    $this->assertResponseRedirects();
  }
}
