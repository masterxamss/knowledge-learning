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

class LessonsFixtures extends Fixture implements DependentFixtureInterface
{
  public function load(ObjectManager $manager): void
  {
    $projectRoot = dirname(__DIR__, 2);

    $lessonsFile = $projectRoot . '/src/DataFixtures/data/lessons.json';

    $dataLessons = json_decode(file_get_contents($lessonsFile), true);
    foreach ($dataLessons as $dataLesson) {
      $lesson = new Lessons();

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
      $this->addReference('lesson_' . $dataLesson['reference'], $lesson);

      $manager->persist($lesson);
    }

    $manager->flush();
  }

  public function getDependencies(): array
  {
    return [CoursesFixtures::class, BadgesFixtures::class];
  }
}
