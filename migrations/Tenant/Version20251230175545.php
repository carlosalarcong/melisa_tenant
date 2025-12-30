<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251230175545 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE account_payment (id INT AUTO_INCREMENT NOT NULL, patient_id INT DEFAULT NULL, cashier_id INT DEFAULT NULL, payment_date DATETIME DEFAULT NULL, total_amount NUMERIC(10, 2) DEFAULT NULL, payment_status_id INT DEFAULT NULL, is_active TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cash_register (id INT AUTO_INCREMENT NOT NULL, member_id INT DEFAULT NULL, opening_date DATETIME DEFAULT NULL, closing_date DATETIME DEFAULT NULL, reopen_date DATETIME DEFAULT NULL, reopen_status_id INT DEFAULT NULL, initial_amount NUMERIC(10, 2) DEFAULT NULL, is_active TINYINT(1) NOT NULL, INDEX IDX_3D7AB1D97597D3FE (member_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cashier_station (id INT AUTO_INCREMENT NOT NULL, member_id INT NOT NULL, initial_amount INT NOT NULL, is_active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_F07302E17597D3FE (member_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment_adjustment (id INT AUTO_INCREMENT NOT NULL, cancellation_date DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', request_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', authorization_date DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', total_amount NUMERIC(10, 2) NOT NULL, discount_amount NUMERIC(10, 2) NOT NULL, total_with_discount NUMERIC(10, 2) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payment_method (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, code VARCHAR(50) DEFAULT NULL, is_active TINYINT(1) NOT NULL, requires_bank TINYINT(1) NOT NULL, requires_document TINYINT(1) NOT NULL, payment_type_id INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE system_parameter (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, value LONGTEXT NOT NULL, description VARCHAR(500) DEFAULT NULL, is_active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cash_register ADD CONSTRAINT FK_3D7AB1D97597D3FE FOREIGN KEY (member_id) REFERENCES `member` (id)');
        $this->addSql('ALTER TABLE cashier_station ADD CONSTRAINT FK_F07302E17597D3FE FOREIGN KEY (member_id) REFERENCES `member` (id)');
        $this->addSql('DROP INDEX IDX_34DCD17632C8A3DE ON person');
        $this->addSql('ALTER TABLE person DROP organization_id');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD176E559F9BF FOREIGN KEY (marital_status_id) REFERENCES `marital_status` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cash_register DROP FOREIGN KEY FK_3D7AB1D97597D3FE');
        $this->addSql('ALTER TABLE cashier_station DROP FOREIGN KEY FK_F07302E17597D3FE');
        $this->addSql('DROP TABLE account_payment');
        $this->addSql('DROP TABLE cash_register');
        $this->addSql('DROP TABLE cashier_station');
        $this->addSql('DROP TABLE payment_adjustment');
        $this->addSql('DROP TABLE payment_method');
        $this->addSql('DROP TABLE system_parameter');
        $this->addSql('ALTER TABLE `person` DROP FOREIGN KEY FK_34DCD176E559F9BF');
        $this->addSql('ALTER TABLE `person` ADD organization_id INT DEFAULT NULL');
        $this->addSql('CREATE INDEX IDX_34DCD17632C8A3DE ON `person` (organization_id)');
    }
}
