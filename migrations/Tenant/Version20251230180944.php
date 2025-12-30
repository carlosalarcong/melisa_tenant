<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251230180944 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE organization (id INT AUTO_INCREMENT NOT NULL, state_id INT DEFAULT NULL, tax_id INT NOT NULL, tax_id_verification_digit VARCHAR(1) NOT NULL, name VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, website VARCHAR(255) DEFAULT NULL, landline_phone VARCHAR(45) DEFAULT NULL, mobile_phone VARCHAR(45) DEFAULT NULL, logo_path VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, contact_person VARCHAR(255) DEFAULT NULL, license_quantity INT NOT NULL, INDEX IDX_C1EE637C5D83CC1 (state_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE state (id INT NOT NULL, name VARCHAR(45) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE organization ADD CONSTRAINT FK_C1EE637C5D83CC1 FOREIGN KEY (state_id) REFERENCES state (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE organization DROP FOREIGN KEY FK_C1EE637C5D83CC1');
        $this->addSql('DROP TABLE organization');
        $this->addSql('DROP TABLE state');
    }
}
