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

class CoursesFixtures extends Fixture implements DependentFixtureInterface
{
  public function load(ObjectManager $manager): void
  {
    $projectRoot = dirname(__DIR__, 2);

    $coursesFile = $projectRoot . '/src/DataFixtures/data/courses.json';

    $dataCourses = json_decode(file_get_contents($coursesFile), true);
    foreach ($dataCourses as $dataCourse) {
      $course = new Courses();

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
      $this->addReference('course_' . $dataCourse['reference'], $course);

      $manager->persist($course);
    }

    $manager->flush();
  }

  public function getDependencies(): array
  {
    return [ThemesFixtures::class, BadgesFixtures::class];
  }
}
