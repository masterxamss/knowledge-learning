<?php

namespace App\DataFixtures;

use App\Entity\Themes;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ThemesFixtures extends Fixture
{
  public function load(ObjectManager $manager): void
  {
    $projectRoot = dirname(__DIR__, 2);

    $themesFile = $projectRoot . '/src/DataFixtures/data/themes.json';

    $dataThemes = json_decode(file_get_contents($themesFile), true);
    foreach ($dataThemes as $dataTheme) {
      $theme = new Themes();
      $theme->setName($dataTheme['name']);
      $theme->setDescription($dataTheme['description']);
      $theme->setImage($dataTheme['image']);
      $theme->setCreatedAt(new \DateTimeImmutable($dataTheme['created_at']));
      $theme->setSlug($dataTheme['slug']);
      $theme->setTitle($dataTheme['title']);
      $theme->setHighlight($dataTheme['highlight']);
      $this->addReference('theme_' . $dataTheme['reference'], $theme);

      $manager->persist($theme);
    }

    $manager->flush();
  }
}
