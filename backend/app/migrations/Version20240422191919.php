<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240422191919 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adds an initial admin user to the admin table with pre-defined credentials.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO admin (`email`, `password`, `name`, `created_at`, `updated_at`, `status`) 
            VALUES (?, ?, ?, NOW(), NOW(), ?)", [
            'admin@ninjastic.pro',
            '5dad47a0af6af3b68e7c3dd079cd9f0ea11a7c47959bc83836171a0031acd92d',
            'Admin',
            1
        ]);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DELETE FROM `admin` WHERE email = ?', ['admin@ninjastic.pro']);
    }
}
