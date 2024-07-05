<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240704171845 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EEA76ED395');
        $this->addSql('DROP INDEX IDX_2FB3D0EEA76ED395 ON project');
        $this->addSql('ALTER TABLE project DROP user_id, DROP created_at, DROP updated_at, DROP client_name, CHANGE project_name name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE rendering DROP FOREIGN KEY FK_17F1F981A76ED395');
        $this->addSql('DROP INDEX IDX_17F1F981A76ED395 ON rendering');
        $this->addSql('ALTER TABLE rendering DROP user_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project ADD user_id INT NOT NULL, ADD created_at DATETIME DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL, ADD client_name VARCHAR(255) DEFAULT NULL, CHANGE name project_name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_2FB3D0EEA76ED395 ON project (user_id)');
        $this->addSql('ALTER TABLE rendering ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE rendering ADD CONSTRAINT FK_17F1F981A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_17F1F981A76ED395 ON rendering (user_id)');
    }
}
