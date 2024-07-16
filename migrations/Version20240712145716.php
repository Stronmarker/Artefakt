<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240712145716 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rendering DROP FOREIGN KEY FK_17F1F981A76ED395');
        $this->addSql('DROP INDEX IDX_17F1F981A76ED395 ON rendering');
        $this->addSql('ALTER TABLE rendering DROP user_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rendering ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE rendering ADD CONSTRAINT FK_17F1F981A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_17F1F981A76ED395 ON rendering (user_id)');
    }
}
