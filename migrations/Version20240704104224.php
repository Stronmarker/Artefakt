<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240704104225 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Handle invalid user_id in rendering and project tables';
    }

    public function up(Schema $schema): void
    {
        // Supprimer les rendus avec user_id non valide
        $this->addSql('DELETE FROM rendering WHERE user_id IS NULL OR user_id NOT IN (SELECT id FROM user)');

        // Supprimer les projets avec user_id non valide
        $this->addSql('DELETE FROM project WHERE user_id IS NULL OR user_id NOT IN (SELECT id FROM user)');

        // Ajouter la contrainte de clé étrangère après avoir nettoyé les données
        $this->addSql('ALTER TABLE rendering ADD CONSTRAINT FK_17F1F981A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_17F1F981A76ED395 ON rendering (user_id)');
    }

    public function down(Schema $schema): void
    {
        // Supprimer les contraintes de clé étrangère lors du rollback
        $this->addSql('ALTER TABLE rendering DROP FOREIGN KEY FK_17F1F981A76ED395');
        $this->addSql('DROP INDEX IDX_17F1F981A76ED395 ON rendering');
    }
}
