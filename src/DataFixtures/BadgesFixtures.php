<?php

namespace App\DataFixtures;

use App\Entity\Badges;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Fixtures for loading badge data into the database.
 * 
 * This class is responsible for loading badge data into the database by reading from a JSON file and creating
 * `Badges` entities. It then persists these entities to the database.
 */
class BadgesFixtures extends Fixture
{
  /**
   * Loads badge data from a JSON file and persists them into the database.
   *
   * This method reads the badges data from a JSON file, creates `Badges` entities, and sets their properties.
   * It then persists the `Badges` entities to the database using Doctrine's ObjectManager.
   *
   * @param ObjectManager $manager The Doctrine ObjectManager to persist the entities.
   * 
   * @return void
   */
  public function load(ObjectManager $manager): void
  {
    // Get the project root directory
    $projectRoot = dirname(__DIR__, 2);

    // Path to the badges JSON data file
    $badgesFile = $projectRoot . '/src/DataFixtures/data/badges.json';

    // Decode the badges data from the JSON file
    $dataBadges = json_decode(file_get_contents($badgesFile), true);

    // Iterate through each badge and create the corresponding entity
    foreach ($dataBadges as $dataBadge) {
      $badge = new Badges();
      $badge->setName($dataBadge['name']);
      $badge->setCreatedAt(new \DateTimeImmutable($dataBadge['created_at']));
      $badge->setUpdatedAt(new \DateTimeImmutable($dataBadge['updated_at']));
      $badge->setCssClass($dataBadge['css_class']);

      // Add reference for future use in other fixtures
      $this->addReference('badge_' . $dataBadge['reference'], $badge);

      // Persist the badge entity
      $manager->persist($badge);
    }

    // Flush the changes to the database
    $manager->flush();
  }
}
