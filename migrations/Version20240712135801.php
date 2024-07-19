<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240712135801 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD address VARCHAR(255) DEFAULT NULL, ADD city VARCHAR(255) DEFAULT NULL, ADD state INT DEFAULT NULL, ADD postal_code INT DEFAULT NULL, ADD country VARCHAR(255) DEFAULT NULL, DROP adresse, DROP ville, DROP département, DROP code_postal, DROP pays');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD adresse VARCHAR(255) DEFAULT NULL, ADD ville VARCHAR(255) DEFAULT NULL, ADD département INT DEFAULT NULL, ADD code_postal INT DEFAULT NULL, ADD pays VARCHAR(255) DEFAULT NULL, DROP address, DROP city, DROP state, DROP postal_code, DROP country');
    }
}
