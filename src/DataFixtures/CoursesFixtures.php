<?php

namespace App\DataFixtures;

use App\Entity\Courses;
use App\Entity\Themes;
use App\Entity\Badges;
use App\DataFixtures\ThemesFixtures;
use App\DataFixtures\BadgesFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Fixtures for loading course data into the database.
 * 
 * This class is responsible for loading course data into the database by reading from a JSON file,
 * creating `Courses` entities, associating them with the corresponding `Themes` and `Badges` entities,
 * and persisting them into the database.
 */
class CoursesFixtures extends Fixture implements DependentFixtureInterface
{
  /**
   * Loads course data from a JSON file and persists them into the database.
   * 
   * This method reads the course data from a JSON file, creates `Courses` entities, and sets their properties.
   * Each course is associated with a `Theme` and a `Badge` entity by their respective IDs. The method persists the 
   * course entities into the database using Doctrine's ObjectManager.
   * 
   * @param ObjectManager $manager The Doctrine ObjectManager to persist the entities.
   * 
   * @return void
   */
  public function load(ObjectManager $manager): void
  {
    // Get the project root directory
    $projectRoot = dirname(__DIR__, 2);

    // Path to the courses JSON data file
    $coursesFile = $projectRoot . '/src/DataFixtures/data/courses.json';

    // Decode the courses data from the JSON file
    $dataCourses = json_decode(file_get_contents($coursesFile), true);

    // Iterate through each course and create the corresponding entity
    foreach ($dataCourses as $dataCourse) {
      $course = new Courses();

      // Set the theme and badge references for the course
      $course->setTheme($this->getReference('theme_' . $dataCourse['theme_id'], Themes::class));
      $course->setTitle($dataCourse['title']);
      $course->setDescription($dataCourse['description']);
      $course->setImage($dataCourse['image']);
      $course->setPrice($dataCourse['price']);
      $course->setCreatedAt(new \DateTimeImmutable($dataCourse['created_at']));
      $course->setUpdatedAt(new \DateTimeImmutable($dataCourse['updated_at']));
      $course->setSlug($dataCourse['slug']);
      $course->setHighlight($dataCourse['highlight']);
      $course->setBadge($this->getReference('badge_' . $dataCourse['badge'], Badges::class));

      // Add reference for the course
      $this->addReference('course_' . $dataCourse['reference'], $course);

      // Persist the course entity
      $manager->persist($course);
    }

    // Flush the changes to the database
    $manager->flush();
  }

  /**
   * Returns the list of dependencies for this fixture.
   * 
   * This method ensures that the `ThemesFixtures` and `BadgesFixtures` classes are loaded before this fixture,
   * as it relies on `Themes` and `Badges` entities being already persisted.
   * 
   * @return array The list of fixture dependencies.
   */
  public function getDependencies(): array
  {
    return [ThemesFixtures::class, BadgesFixtures::class];
  }
}
