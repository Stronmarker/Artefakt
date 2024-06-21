<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240620114949 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EE19EB6921');
        $this->addSql('DROP INDEX UNIQ_2FB3D0EE19EB6921 ON project');
        $this->addSql('ALTER TABLE project ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL, DROP client_id, DROP creation_date, DROP modification_date, CHANGE client_name client_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE rendering CHANGE toward_svg lamination_svg VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project ADD client_id INT DEFAULT NULL, ADD creation_date DATE NOT NULL, ADD modification_date DATE NOT NULL, DROP created_at, DROP updated_at, CHANGE client_name client_name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EE19EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2FB3D0EE19EB6921 ON project (client_id)');
        $this->addSql('ALTER TABLE rendering CHANGE lamination_svg toward_svg VARCHAR(255) DEFAULT NULL');
    }
}
