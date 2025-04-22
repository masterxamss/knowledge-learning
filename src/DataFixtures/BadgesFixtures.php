<?php

namespace App\DataFixtures;

use App\Entity\Badges;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BadgesFixtures extends Fixture
{
  public function load(ObjectManager $manager): void
  {
    $projectRoot = dirname(__DIR__, 2);

    $badgesFile = $projectRoot . '/src/DataFixtures/data/badges.json';

    $dataBadges = json_decode(file_get_contents($badgesFile), true);

    foreach ($dataBadges as $dataBadge) {
      $badge = new Badges();
      $badge->setName($dataBadge['name']);
      $badge->setCreatedAt(new \DateTimeImmutable($dataBadge['created_at']));
      $badge->setUpdatedAt(new \DateTimeImmutable($dataBadge['updated_at']));
      $badge->setCssClass($dataBadge['css_class']);
      $this->addReference('badge_' . $dataBadge['reference'], $badge);

      $manager->persist($badge);
    }

    $manager->flush();
  }
}
