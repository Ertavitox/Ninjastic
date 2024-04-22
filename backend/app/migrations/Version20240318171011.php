<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240318171011 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `admin` ADD password VARCHAR(255) NOT NULL, CHANGE status status INT DEFAULT 1 NOT NULL');
        $this->addSql('ALTER TABLE comment CHANGE status status INT DEFAULT 1 NOT NULL');
        $this->addSql('ALTER TABLE topic CHANGE status status INT DEFAULT 1 NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE status status INT DEFAULT 1 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE topic CHANGE status status INT NOT NULL');
        $this->addSql('ALTER TABLE `admin` DROP password, CHANGE status status INT NOT NULL');
        $this->addSql('ALTER TABLE comment CHANGE status status INT NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE status status INT NOT NULL');
    }
}
