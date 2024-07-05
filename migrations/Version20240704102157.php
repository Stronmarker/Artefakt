<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240704102157 extends AbstractMigration
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
        $this->addSql('ALTER TABLE rendering ADD rendering_name VARCHAR(255) NOT NULL, ADD images JSON NOT NULL, DROP user_id, DROP front_png, DROP toward_png, DROP gilding_svg, DROP lamination_svg, DROP dimensions, DROP link');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rendering ADD user_id INT DEFAULT NULL, ADD toward_png VARCHAR(255) NOT NULL, ADD gilding_svg VARCHAR(255) DEFAULT NULL, ADD lamination_svg VARCHAR(255) DEFAULT NULL, ADD dimensions VARCHAR(255) NOT NULL, ADD link VARCHAR(255) DEFAULT NULL, DROP images, CHANGE rendering_name front_png VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE rendering ADD CONSTRAINT FK_17F1F981A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_17F1F981A76ED395 ON rendering (user_id)');
    }
}
