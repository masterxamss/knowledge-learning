<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250331171012 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE completion (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, lesson_id INT NOT NULL, progress DOUBLE PRECISION NOT NULL, status VARCHAR(45) NOT NULL, completion_date DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_38D6377EA76ED395 (user_id), INDEX IDX_38D6377ECDF80196 (lesson_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE completion ADD CONSTRAINT FK_38D6377EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE completion ADD CONSTRAINT FK_38D6377ECDF80196 FOREIGN KEY (lesson_id) REFERENCES lessons (id)');
        $this->addSql('ALTER TABLE chapters CHANGE content content LONGTEXT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE completion DROP FOREIGN KEY FK_38D6377EA76ED395');
        $this->addSql('ALTER TABLE completion DROP FOREIGN KEY FK_38D6377ECDF80196');
        $this->addSql('DROP TABLE completion');
        $this->addSql('ALTER TABLE chapters CHANGE content content LONGTEXT DEFAULT NULL');
    }
}
