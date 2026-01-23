<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260123122407 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE menu_items (id INT AUTO_INCREMENT NOT NULL, parent_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL, label VARCHAR(100) NOT NULL, route VARCHAR(255) DEFAULT NULL, icon VARCHAR(100) DEFAULT NULL, module VARCHAR(100) DEFAULT NULL, position INT NOT NULL, enabled TINYINT(1) NOT NULL, visible_in_sidebar TINYINT(1) NOT NULL, requires_auth TINYINT(1) NOT NULL, required_roles JSON DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_70B2CA2A727ACA70 (parent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE menu_items ADD CONSTRAINT FK_70B2CA2A727ACA70 FOREIGN KEY (parent_id) REFERENCES menu_items (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE menu_items DROP FOREIGN KEY FK_70B2CA2A727ACA70');
        $this->addSql('DROP TABLE menu_items');
    }
}
