<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240710101940 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rendering ADD gilding_front_png VARCHAR(255) DEFAULT NULL, ADD gilding_toward_png VARCHAR(255) DEFAULT NULL, ADD lamination_front_png VARCHAR(255) DEFAULT NULL, ADD lamination_toward_png VARCHAR(255) DEFAULT NULL, ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL, DROP gilding_svg, DROP lamination_svg');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rendering ADD gilding_svg VARCHAR(255) DEFAULT NULL, ADD lamination_svg VARCHAR(255) DEFAULT NULL, DROP gilding_front_png, DROP gilding_toward_png, DROP lamination_front_png, DROP lamination_toward_png, DROP created_at, DROP updated_at');
    }
}
