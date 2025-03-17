<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250316165406 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE badges (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE chapters CHANGE content content LONGTEXT');
        $this->addSql('ALTER TABLE courses ADD badge INT DEFAULT NULL');
        $this->addSql('ALTER TABLE courses ADD CONSTRAINT FK_A9A55A4CFEF0481D FOREIGN KEY (badge) REFERENCES badges (id)');
        $this->addSql('CREATE INDEX IDX_A9A55A4CFEF0481D ON courses (badge)');
        $this->addSql('ALTER TABLE lessons ADD badge INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lessons ADD CONSTRAINT FK_3F4218D9FEF0481D FOREIGN KEY (badge) REFERENCES badges (id)');
        $this->addSql('CREATE INDEX IDX_3F4218D9FEF0481D ON lessons (badge)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE courses DROP FOREIGN KEY FK_A9A55A4CFEF0481D');
        $this->addSql('ALTER TABLE lessons DROP FOREIGN KEY FK_3F4218D9FEF0481D');
        $this->addSql('DROP TABLE badges');
        $this->addSql('ALTER TABLE chapters CHANGE content content LONGTEXT DEFAULT NULL');
        $this->addSql('DROP INDEX IDX_3F4218D9FEF0481D ON lessons');
        $this->addSql('ALTER TABLE lessons DROP badge');
        $this->addSql('DROP INDEX IDX_A9A55A4CFEF0481D ON courses');
        $this->addSql('ALTER TABLE courses DROP badge');
    }
}
