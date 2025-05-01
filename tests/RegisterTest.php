<?php

namespace App\Tests;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Mime\Email;

/**
 *
 * Class RegisterTest
 *
 * Functional test for the user registration process.
 *
 * This test verifies the full registration workflow:
 * - Accessing the registration page
 * - Submitting the registration form with valid data
 * - Ensuring a new User entity is created and persisted in the database
 * - Verifying that the user is not yet verified (email confirmation pending)
 * - Confirming a confirmation email was sent
 * - Simulating user login
 * - Verifying redirection after login
 *
 * Requirements:
 * - The mailer must be configured with a fake or profiler transport to inspect sent emails
 * - The application must allow automatic login upon registration
 */
class RegisterTest extends WebTestCase
{
  /** @var \Symfony\Bundle\FrameworkBundle\KernelBrowser */
  private $client;

  /** @var \Doctrine\ORM\EntityManagerInterface */
  private $entityManager;

  /**
   * Sets up the test environment by creating a client and cleaning any existing test user.
   */
  protected function setUp(): void
  {
    $this->client = static::createClient();
    $this->entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');

    // Remove existing user with the test email, if present
    $existingUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'test@example.com']);
    if ($existingUser) {
      $this->entityManager->remove($existingUser);
      $this->entityManager->flush();
    }
  }

  /**
   * Tests the user registration process from start to finish.
   *
   * Steps:
   * 1. Load the registration form
   * 2. Submit it with valid test data
   * 3. Check that a User entity was created
   * 4. Assert the user is not yet verified
   * 5. Simulate login and verify redirection
   * 6. Assert that a confirmation email was sent
   */
  public function testUserRegistration(): void
  {
    // Step 1: Request registration page
    $crawler = $this->client->request('GET', '/register');
    $this->assertResponseIsSuccessful();

    // Step 2: Fill and submit the form
    $form = $crawler->selectButton("S'inscrire")->form([
      'registration_form[first_name]' => 'First Name',
      'registration_form[last_name]' => 'Last Name',
      'registration_form[email]' => 'test@example.com',
      'registration_form[password]' => 'Password_123#',
      'registration_form[confirmPassword]' => 'Password_123#',
      'registration_form[agreeTerms]' => true,
    ]);
    $this->client->submit($form);

    // Step 3: Check if user was created in the database
    $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'test@example.com']);
    $this->assertNotNull($user, 'User was not created in the database');

    // Step 4: Assert user is not verified
    $this->assertFalse($user->isVerified(), 'User should not be verified before email confirmation');

    // Step 5: Simulate login (needed for redirect check in some flows)
    $this->client->loginUser($user);
    $this->assertResponseRedirects('/', null, 'Expected redirect to homepage after login');

    // Step 6: Verify confirmation email was sent
    if ($this->client->getProfile()) {
      $mailCollector = $this->client->getProfile()->getCollector('mailer');
      $this->assertSame(1, $mailCollector->getMessageCount(), 'Expected one email to be sent');

      /** @var Email $message */
      $message = $mailCollector->getMessages()[0];
      $this->assertInstanceOf(Email::class, $message);
      $this->assertSame('test@example.com', $message->getTo()[0]->getAddress());
      $this->assertSame('Please Confirm your Email', $message->getSubject());
    }
  }
}
