<?php

namespace App\DataFixtures;

use App\Entity\Themes;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Fixtures for loading theme data into the database.
 * 
 * This class is responsible for loading theme data into the database by reading from a JSON file,
 * creating `Themes` entities, and persisting them into the database.
 */
class ThemesFixtures extends Fixture
{
  /**
   * Loads theme data from a JSON file and persists them into the database.
   * 
   * This method reads the theme data from a JSON file, creates `Themes` entities, and sets their properties.
   * The method persists the theme entities into the database using Doctrine's ObjectManager.
   * 
   * @param ObjectManager $manager The Doctrine ObjectManager to persist the entities.
   * 
   * @return void
   */
  public function load(ObjectManager $manager): void
  {
    // Get the project root directory
    $projectRoot = dirname(__DIR__, 2);

    // Path to the themes JSON data file
    $themesFile = $projectRoot . '/src/DataFixtures/data/themes.json';

    // Decode the themes data from the JSON file
    $dataThemes = json_decode(file_get_contents($themesFile), true);

    // Iterate through each theme and create the corresponding entity
    foreach ($dataThemes as $dataTheme) {
      $theme = new Themes();

      // Set the properties for the theme entity
      $theme->setName($dataTheme['name']);
      $theme->setDescription($dataTheme['description']);
      $theme->setImage($dataTheme['image']);
      $theme->setCreatedAt(new \DateTimeImmutable($dataTheme['created_at']));
      $theme->setSlug($dataTheme['slug']);
      $theme->setTitle($dataTheme['title']);
      $theme->setHighlight($dataTheme['highlight']);

      // Add reference for the theme
      $this->addReference('theme_' . $dataTheme['reference'], $theme);

      // Persist the theme entity
      $manager->persist($theme);
    }

    // Flush the changes to the database
    $manager->flush();
  }
}
