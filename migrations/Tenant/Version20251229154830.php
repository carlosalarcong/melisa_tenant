<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251229154830 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE position (id INT AUTO_INCREMENT NOT NULL, organization_id INT NOT NULL, state_id INT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(100) DEFAULT NULL, snomed_code VARCHAR(50) DEFAULT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_462CE4F532C8A3DE (organization_id), INDEX IDX_462CE4F55D83CC1 (state_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE professional_type (id INT AUTO_INCREMENT NOT NULL, organization_id INT NOT NULL, state_id INT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(100) DEFAULT NULL, description LONGTEXT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_9E9B6EF732C8A3DE (organization_id), INDEX IDX_9E9B6EF75D83CC1 (state_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE position ADD CONSTRAINT FK_462CE4F532C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE position ADD CONSTRAINT FK_462CE4F55D83CC1 FOREIGN KEY (state_id) REFERENCES state (id)');
        $this->addSql('ALTER TABLE professional_type ADD CONSTRAINT FK_9E9B6EF732C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE professional_type ADD CONSTRAINT FK_9E9B6EF75D83CC1 FOREIGN KEY (state_id) REFERENCES state (id)');
        $this->addSql('ALTER TABLE `member` ADD position_id INT DEFAULT NULL, ADD professional_type_id INT DEFAULT NULL, ADD rcm VARCHAR(100) DEFAULT NULL, ADD superintendent_registry VARCHAR(100) DEFAULT NULL, ADD observation LONGTEXT DEFAULT NULL, ADD web_observation LONGTEXT DEFAULT NULL, ADD overbooking_quantity INT DEFAULT 0, ADD is_emergency_professional TINYINT(1) DEFAULT 0, ADD is_integration_professional TINYINT(1) DEFAULT 0');
        $this->addSql('ALTER TABLE `member` ADD CONSTRAINT FK_70E4FA78DD842E46 FOREIGN KEY (position_id) REFERENCES position (id)');
        $this->addSql('ALTER TABLE `member` ADD CONSTRAINT FK_70E4FA784A69AC1E FOREIGN KEY (professional_type_id) REFERENCES professional_type (id)');
        $this->addSql('CREATE INDEX IDX_70E4FA78DD842E46 ON `member` (position_id)');
        $this->addSql('CREATE INDEX IDX_70E4FA784A69AC1E ON `member` (professional_type_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `member` DROP FOREIGN KEY FK_70E4FA78DD842E46');
        $this->addSql('ALTER TABLE `member` DROP FOREIGN KEY FK_70E4FA784A69AC1E');
        $this->addSql('ALTER TABLE position DROP FOREIGN KEY FK_462CE4F532C8A3DE');
        $this->addSql('ALTER TABLE position DROP FOREIGN KEY FK_462CE4F55D83CC1');
        $this->addSql('ALTER TABLE professional_type DROP FOREIGN KEY FK_9E9B6EF732C8A3DE');
        $this->addSql('ALTER TABLE professional_type DROP FOREIGN KEY FK_9E9B6EF75D83CC1');
        $this->addSql('DROP TABLE position');
        $this->addSql('DROP TABLE professional_type');
        $this->addSql('DROP INDEX IDX_70E4FA78DD842E46 ON `member`');
        $this->addSql('DROP INDEX IDX_70E4FA784A69AC1E ON `member`');
        $this->addSql('ALTER TABLE `member` DROP position_id, DROP professional_type_id, DROP rcm, DROP superintendent_registry, DROP observation, DROP web_observation, DROP overbooking_quantity, DROP is_emergency_professional, DROP is_integration_professional');
    }
}
