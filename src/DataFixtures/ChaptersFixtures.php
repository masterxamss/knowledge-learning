<?php

namespace App\DataFixtures;

use App\Entity\Chapters;
use App\Entity\Lessons;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ChaptersFixtures extends Fixture implements DependentFixtureInterface
{
  public function load(ObjectManager $manager): void
  {
    $projectRoot = dirname(__DIR__, 2);

    $chaptersFile = $projectRoot . '/src/DataFixtures/data/chapters.json';

    $dataChapters = json_decode(file_get_contents($chaptersFile), true);
    foreach ($dataChapters as $dataChapter) {
      $chapter = new Chapters();

      $chapter->setLessonId($this->getReference('lesson_' . $dataChapter['lesson_id'], Lessons::class));
      $chapter->setTitle($dataChapter['title']);
      $chapter->setContent($dataChapter['content']);
      $chapter->setCreatedAt(new \DateTimeImmutable($dataChapter['created_at']));
      $chapter->setUpdatedAt(new \DateTimeImmutable($dataChapter['updated_at']));
      $chapter->setImage($dataChapter['image']);
      $chapter->setSlug($dataChapter['slug']);
      $chapter->setVideo($dataChapter['video']);

      $manager->persist($chapter);
    }

    $manager->flush();
  }

  public function getDependencies(): array
  {
    return [LessonsFixtures::class];
  }
}
