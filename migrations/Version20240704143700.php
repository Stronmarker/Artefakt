<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240704143700 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rendering ADD gilding_svg_front VARCHAR(255) DEFAULT NULL, ADD gilding_svg_toward VARCHAR(255) DEFAULT NULL, ADD lamination_svg_front VARCHAR(255) DEFAULT NULL, ADD lamination_svg_toward VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE rendering DROP gilding_svg_front, DROP gilding_svg_toward, DROP lamination_svg_front, DROP lamination_svg_toward');
    }
}
