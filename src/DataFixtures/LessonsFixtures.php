<?php

namespace App\DataFixtures;

use App\Entity\Courses;
use App\Entity\Lessons;
use App\Entity\Badges;
use App\DataFixtures\CoursesFixtures;
use App\DataFixtures\BadgesFixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Fixtures for loading lesson data into the database.
 * 
 * This class is responsible for loading lesson data into the database by reading from a JSON file,
 * creating `Lessons` entities, associating them with the corresponding `Courses` and `Badges` entities,
 * and persisting them into the database.
 */
class LessonsFixtures extends Fixture implements DependentFixtureInterface
{
  /**
   * Loads lesson data from a JSON file and persists them into the database.
   * 
   * This method reads the lesson data from a JSON file, creates `Lessons` entities, and sets their properties.
   * Each lesson is associated with a `Course` and a `Badge` entity by their respective IDs. The method persists 
   * the lesson entities into the database using Doctrine's ObjectManager.
   * 
   * @param ObjectManager $manager The Doctrine ObjectManager to persist the entities.
   * 
   * @return void
   */
  public function load(ObjectManager $manager): void
  {
    // Get the project root directory
    $projectRoot = dirname(__DIR__, 2);

    // Path to the lessons JSON data file
    $lessonsFile = $projectRoot . '/src/DataFixtures/data/lessons.json';

    // Decode the lessons data from the JSON file
    $dataLessons = json_decode(file_get_contents($lessonsFile), true);

    // Iterate through each lesson and create the corresponding entity
    foreach ($dataLessons as $dataLesson) {
      $lesson = new Lessons();

      // Set the course and badge references for the lesson
      $lesson->setCourse($this->getReference('course_' . $dataLesson['course_id'], Courses::class));
      $lesson->setTitle($dataLesson['title']);
      $lesson->setDescription($dataLesson['description']);
      $lesson->setPrice($dataLesson['price']);
      $lesson->setSlug($dataLesson['slug']);
      $lesson->setCreatedAt(new \DateTimeImmutable($dataLesson['created_at']));
      $lesson->setUpdatedAt(new \DateTimeImmutable($dataLesson['updated_at']));
      $lesson->setHighlight($dataLesson['highlight']);
      $lesson->setImage($dataLesson['image']);
      $lesson->setBadge($this->getReference('badge_' . $dataLesson['badge'], Badges::class));

      // Add reference for the lesson
      $this->addReference('lesson_' . $dataLesson['reference'], $lesson);

      // Persist the lesson entity
      $manager->persist($lesson);
    }

    // Flush the changes to the database
    $manager->flush();
  }

  /**
   * Returns the list of dependencies for this fixture.
   * 
   * This method ensures that the `CoursesFixtures` and `BadgesFixtures` classes are loaded before this fixture,
   * as it relies on `Courses` and `Badges` entities being already persisted.
   * 
   * @return array The list of fixture dependencies.
   */
  public function getDependencies(): array
  {
    return [CoursesFixtures::class, BadgesFixtures::class];
  }
}
