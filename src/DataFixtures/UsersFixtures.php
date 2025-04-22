<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UsersFixtures extends Fixture
{
  private $paswordHasher;

  public function __construct(UserPasswordHasherInterface $passwordHasher)
  {
    $this->paswordHasher = $passwordHasher;
  }

  public function load(ObjectManager $manager): void
  {
    $projectRoot = dirname(__DIR__, 2);

    $usersFile = $projectRoot . '/src/DataFixtures/data/users.json';

    $dataUsers = json_decode(file_get_contents($usersFile), true);

    foreach ($dataUsers as $dataUser) {
      $user = new User();
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

      $manager->persist($user);
    }

    $manager->flush();
  }
}
