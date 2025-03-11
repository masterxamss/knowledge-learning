<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250311112621 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE courses DROP FOREIGN KEY FK_A9A55A4C276615B2');
        $this->addSql('DROP INDEX IDX_A9A55A4C276615B2 ON courses');
        $this->addSql('ALTER TABLE courses CHANGE description description LONGTEXT, CHANGE price price NUMERIC(10, 2) NOT NULL, CHANGE theme_id_id theme_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE courses ADD CONSTRAINT FK_A9A55A4C59027487 FOREIGN KEY (theme_id) REFERENCES themes (id)');
        $this->addSql('CREATE INDEX IDX_A9A55A4C59027487 ON courses (theme_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE courses DROP FOREIGN KEY FK_A9A55A4C59027487');
        $this->addSql('DROP INDEX IDX_A9A55A4C59027487 ON courses');
        $this->addSql('ALTER TABLE courses CHANGE description description VARCHAR(255) NOT NULL, CHANGE price price NUMERIC(10, 0) NOT NULL, CHANGE theme_id theme_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE courses ADD CONSTRAINT FK_A9A55A4C276615B2 FOREIGN KEY (theme_id_id) REFERENCES themes (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_A9A55A4C276615B2 ON courses (theme_id_id)');
    }
}
