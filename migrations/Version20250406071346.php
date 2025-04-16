<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250406071346 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE certifications (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, course_id INT NOT NULL, date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_3B0D76D5A76ED395 (user_id), INDEX IDX_3B0D76D5591CC992 (course_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE certifications ADD CONSTRAINT FK_3B0D76D5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE certifications ADD CONSTRAINT FK_3B0D76D5591CC992 FOREIGN KEY (course_id) REFERENCES courses (id)');
        $this->addSql('ALTER TABLE chapters CHANGE content content LONGTEXT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE certifications DROP FOREIGN KEY FK_3B0D76D5A76ED395');
        $this->addSql('ALTER TABLE certifications DROP FOREIGN KEY FK_3B0D76D5591CC992');
        $this->addSql('DROP TABLE certifications');
        $this->addSql('ALTER TABLE chapters CHANGE content content LONGTEXT DEFAULT NULL');
    }
}
