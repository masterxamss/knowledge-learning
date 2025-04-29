<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Fixtures for loading user data into the database.
 * 
 * This class is responsible for loading user data into the database by reading from a JSON file,
 * creating `User` entities, hashing the passwords, and persisting them into the database.
 */
class UsersFixtures extends Fixture
{
  /**
   * @var UserPasswordHasherInterface
   */
  private $paswordHasher;

  /**
   * UsersFixtures constructor.
   *
   * @param UserPasswordHasherInterface $passwordHasher The password hasher service for hashing user passwords.
   */
  public function __construct(UserPasswordHasherInterface $passwordHasher)
  {
    $this->paswordHasher = $passwordHasher;
  }

  /**
   * Loads user data from a JSON file and persists them into the database.
   * 
   * This method reads the user data from a JSON file, creates `User` entities, hashes their passwords,
   * and sets their properties. The method persists the user entities into the database using Doctrine's ObjectManager.
   * 
   * @param ObjectManager $manager The Doctrine ObjectManager to persist the entities.
   * 
   * @return void
   */
  public function load(ObjectManager $manager): void
  {
    // Get the project root directory
    $projectRoot = dirname(__DIR__, 2);

    // Path to the users JSON data file
    $usersFile = $projectRoot . '/src/DataFixtures/data/users.json';

    // Decode the users data from the JSON file
    $dataUsers = json_decode(file_get_contents($usersFile), true);

    // Iterate through each user and create the corresponding entity
    foreach ($dataUsers as $dataUser) {
      $user = new User();

      // Set the properties for the user entity
      $user->setEmail($dataUser['email']);
      $user->setRoles($dataUser['roles']);
      $user->setPassword($this->paswordHasher->hashPassword($user, $dataUser['password']));
      $user->setFirstName($dataUser['first_name']);
      $user->setLastName($dataUser['last_name']);
      $user->setAddress($dataUser['address']);
      $user->setIsVerified($dataUser['is_verified']);
      $user->setActive($dataUser['active']);
      $user->setCreatedAt(new \DateTimeImmutable($dataUser['created_at']));
      $user->setUpdatedAt(new \DateTimeImmutable($dataUser['updated_at']));
      $user->setImage($dataUser['image']);
      $user->setTitle($dataUser['title']);
      $user->setDescription($dataUser['description']);
      $user->setLinks($dataUser['links']);

      // Persist the user entity
      $manager->persist($user);
    }

    // Flush the changes to the database
    $manager->flush();
  }
}
