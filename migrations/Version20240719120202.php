<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240719120202 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_17F1F981989D9B62 ON rendering');
        $this->addSql('ALTER TABLE rendering ADD is_accepted TINYINT(1) NOT NULL, ADD reject_reason LONGTEXT DEFAULT NULL, DROP slug');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rendering ADD slug VARCHAR(255) NOT NULL, DROP is_accepted, DROP reject_reason');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_17F1F981989D9B62 ON rendering (slug)');
    }
}
