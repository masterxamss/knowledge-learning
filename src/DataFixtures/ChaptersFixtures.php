<?php

namespace App\DataFixtures;

use App\Entity\Chapters;
use App\Entity\Lessons;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Fixtures for loading chapter data into the database.
 * 
 * This class is responsible for loading chapter data into the database by reading from a JSON file,
 * creating `Chapters` entities, and associating them with corresponding `Lessons` entities.
 * It also handles persisting these entities to the database.
 */
class ChaptersFixtures extends Fixture implements DependentFixtureInterface
{
  /**
   * Loads chapter data from a JSON file and persists them into the database.
   * 
   * This method reads the chapter data from a JSON file, creates `Chapters` entities, and sets their properties.
   * Each chapter is associated with a `Lesson` entity by its `lesson_id`. The method persists the chapter entities 
   * into the database using Doctrine's ObjectManager.
   * 
   * @param ObjectManager $manager The Doctrine ObjectManager to persist the entities.
   * 
   * @return void
   */
  public function load(ObjectManager $manager): void
  {
    // Get the project root directory
    $projectRoot = dirname(__DIR__, 2);

    // Path to the chapters JSON data file
    $chaptersFile = $projectRoot . '/src/DataFixtures/data/chapters.json';

    // Decode the chapters data from the JSON file
    $dataChapters = json_decode(file_get_contents($chaptersFile), true);

    // Iterate through each chapter and create the corresponding entity
    foreach ($dataChapters as $dataChapter) {
      $chapter = new Chapters();

      // Set the lesson reference for the chapter
      $chapter->setLessonId($this->getReference('lesson_' . $dataChapter['lesson_id'], Lessons::class));
      $chapter->setTitle($dataChapter['title']);
      $chapter->setContent($dataChapter['content']);
      $chapter->setCreatedAt(new \DateTimeImmutable($dataChapter['created_at']));
      $chapter->setUpdatedAt(new \DateTimeImmutable($dataChapter['updated_at']));
      $chapter->setImage($dataChapter['image']);
      $chapter->setSlug($dataChapter['slug']);
      $chapter->setVideo($dataChapter['video']);

      // Persist the chapter entity
      $manager->persist($chapter);
    }

    // Flush the changes to the database
    $manager->flush();
  }

  /**
   * Returns the list of dependencies for this fixture.
   * 
   * This method ensures that the `LessonsFixtures` class is loaded before this fixture,
   * as it relies on the `Lessons` entities being already persisted.
   * 
   * @return array The list of fixture dependencies.
   */
  public function getDependencies(): array
  {
    return [LessonsFixtures::class];
  }
}
