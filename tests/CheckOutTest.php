<?php

namespace App\Tests;

use App\Entity\Cart;
use App\Entity\Order;
use App\Entity\Courses;
use App\Entity\Lessons;
use App\Entity\User;
use App\Entity\Themes;
use App\Service\StripeServiceInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class CheckoutTest
 * 
 * This test suite validates the checkout flow, including:
 * 
 * 1. Creating a test user and logging them in.
 * 2. Creating a test product (course and lesson) and adding them to the user's cart.
 * 3. Configuring a mock Stripe service to simulate payment processing.
 * 4. Sending a request to the checkout controller.
 * 5. Verifying the redirection to Stripe's checkout URL.
 * 6. Validating the creation of an order in the database.
 */
class CheckoutTest extends WebTestCase
{
  /**
   * @var \Symfony\Bundle\FrameworkBundle\KernelBrowser $client
   * Client for simulating HTTP requests.
   */
  private $client;

  /**
   * @var \Doctrine\ORM\EntityManagerInterface $entityManager
   * Entity manager for database operations.
   */
  private $entityManager;

  /**
   * @var \PHPUnit\Framework\MockObject\MockObject|StripeServiceInterface $stripeServiceMock
   * Mock object for the StripeService.
   */
  private $stripeServiceMock;

  /**
   * Set up the test environment.
   * Initializes the client, entity manager, and StripeService mock.
   * 
   * @return void
   */
  protected function setUp(): void
  {
    $this->client = static::createClient();
    $this->entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');

    // Create mock for StripeService
    $this->stripeServiceMock = $this->createMock(StripeServiceInterface::class);
    $this->client->getContainer()->set(StripeServiceInterface::class, $this->stripeServiceMock);
  }

  /**
   * Tear down the test environment.
   * 
   * @return void
   */
  protected function tearDown(): void
  {
    parent::tearDown();

    if ($this->entityManager) {
      $this->entityManager->close();
    }
    $this->entityManager = null;
  }

  /**
   * Test the checkout flow with a mock Stripe service.
   * Verifies user creation, product addition, and order generation.
   * 
   * @return void
   */
  public function testCheckoutFlowWithStripe(): void
  {
    // 1. Create test user and login
    $user = $this->createTestUser();
    $this->client->loginUser($user);

    // 2. Create test product and add to cart
    $theme = $this->createTestTheme();
    $course = $this->createTestCourse($theme);
    $lesson = $this->createTestLesson($course);
    $this->addLessonToCart($lesson, $user);
    $this->addCourseToCart($course, $user);

    // Configure mock for StripeService
    $this->stripeServiceMock->method('createPayment')
      ->willReturn('https://checkout.stripe.com/test-session-url');

    // 3. Make a request to controller
    $this->client->request('GET', '/checkout');

    // 4. Verify redirect to Stripe
    $this->assertResponseStatusCodeSame(303); // HTTP_SEE_OTHER
    $this->assertEquals(
      'https://checkout.stripe.com/test-session-url',
      $this->client->getResponse()->headers->get('Location')
    );

    // 5. Verify order creation
    $order = $this->entityManager->getRepository(Order::class)->findOneBy(['user' => $user]);
    $this->assertNotNull($order);
    $this->assertSame(200.00, round($order->getTotalPrice(), 2));
  }

  /**
   * Create a test user.
   * 
   * @return User The created user entity.
   */
  private function createTestUser(): User
  {
    $user = new User();
    $user->setEmail('TmDg1@example.com');
    $user->setRoles(['ROLE_USER']);
    $user->setPassword(password_hash('password', PASSWORD_BCRYPT));
    $user->setFirstName('John');
    $user->setLastName('Doe');
    $user->setAddress([
      'street'      => '123 Main St',
      'complement'  => 'Apt 4B',
      'city'        => 'San Francisco',
      'zipcode'     => '94105',
      'state'       => 'CA',
      'country'     => 'United States',
    ]);
    $user->setIsVerified(true);
    $user->setActive(true);
    $user->setCreatedAt(new \DateTimeImmutable());
    $user->setUpdatedAt(new \DateTimeImmutable());
    $user->setLinks([
      'github'    => 'https://github.com/johndoe',
      'linkedin'  => 'https://linkedin.com/johndoe',
      'website'   => 'https://johndoe.com',
      'twitter'   => 'https://twitter.com/johndoe',
      'youtube'   => 'https://youtube.com/johndoe',
      'facebook'  => 'https://facebook.com/johndoe',
    ]);

    $this->entityManager->persist($user);
    $this->entityManager->flush();

    return $user;
  }

  /**
   * Create a test theme.
   * 
   * @return Themes The created themes entity.
   */
  private function createTestTheme(): Themes
  {
    $theme = new Themes();
    $theme->setName('Test Theme');
    $theme->setCreatedAt(new \DateTimeImmutable());
    $theme->setSlug('test-theme');
    $theme->setTitle('Test Theme');

    $this->entityManager->persist($theme);
    $this->entityManager->flush();

    return $theme;
  }

  /**
   * Create a test course.
   * 
   * @return Courses The created course entity.
   */
  private function createTestCourse(Themes $theme): Courses
  {
    $course = new Courses();
    $course->setTheme($theme);
    $course->setTitle('Test Course');
    $course->setPrice(100.00);
    $course->setSlug('test-course');

    $this->entityManager->persist($course);
    $this->entityManager->flush();

    return $course;
  }

  /**
   * Create a test Lesson.
   * 
   * @return Lessons The created lesson entity.
   */
  private function createTestLesson(Courses $course): Lessons
  {
    $lesson = new Lessons();
    $lesson->setCourse($course);
    $lesson->setTitle('Test Lesson');
    $lesson->setSlug('test-lesson');
    $lesson->setPrice(100.00);
    $lesson->setCreatedAt(new \DateTimeImmutable());
    $lesson->setUpdatedAt(new \DateTimeImmutable());

    $this->entityManager->persist($lesson);
    $this->entityManager->flush();

    return $lesson;
  }

  /**
   * Add a lesson to the cart for a specific user.
   * 
   * @param Lessons $lesson The lesson
   */
  private function addLessonToCart(Lessons $lesson, User $user): void
  {
    $cart = new Cart();
    $cart->setUser($user);
    $cart->setLesson($lesson);
    $cart->setPrice($lesson->getPrice());
    $cart->setTotal($lesson->getPrice());

    $this->entityManager->persist($cart);
    $this->entityManager->flush();
  }

  /**
   * Add a course to the cart for a specific user.
   * 
   * @param Courses $course The course
   */
  private function addCourseToCart(Courses $course, User $user): void
  {
    $cart = new Cart();
    $cart->setUser($user);
    $cart->setCourse($course);
    $cart->setPrice($course->getPrice());
    $cart->setTotal($course->getPrice());

    $this->entityManager->persist($cart);
    $this->entityManager->flush();
  }
}
