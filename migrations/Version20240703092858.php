<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240703092858 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add user_id column to project table and set default user for existing records';
    }

    public function up(Schema $schema): void
    {
        // Vérifiez si la colonne user_id existe déjà
        $table = $schema->getTable('project');
        if (!$table->hasColumn('user_id')) {
            // Ajouter la colonne user_id à la table project
            $this->addSql('ALTER TABLE project ADD user_id INT DEFAULT NULL');
        }

        // Mettre à jour les enregistrements existants avec un utilisateur par défaut
        // Assurez-vous que l'utilisateur avec l'ID 1 existe dans votre table user
        $this->addSql('UPDATE project SET user_id = 1 WHERE user_id IS NULL');

        // Rendre la colonne user_id non nulle et ajouter la contrainte de clé étrangère
        $this->addSql('ALTER TABLE project MODIFY user_id INT NOT NULL');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_2FB3D0EEA76ED395 ON project (user_id)');
    }

    public function down(Schema $schema): void
    {
        // Supprimer la contrainte de clé étrangère et la colonne user_id
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EEA76ED395');
        $this->addSql('DROP INDEX IDX_2FB3D0EEA76ED395 ON project');
        $this->addSql('ALTER TABLE project DROP user_id');
    }
}
