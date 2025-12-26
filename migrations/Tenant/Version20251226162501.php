<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251226162501 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE member_group (id INT AUTO_INCREMENT NOT NULL, member_id INT NOT NULL, group_id INT NOT NULL, INDEX IDX_FE1D1367597D3FE (member_id), INDEX IDX_FE1D136FE54D947 (group_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE member_group ADD CONSTRAINT FK_FE1D1367597D3FE FOREIGN KEY (member_id) REFERENCES `member` (id)');
        $this->addSql('ALTER TABLE member_group ADD CONSTRAINT FK_FE1D136FE54D947 FOREIGN KEY (group_id) REFERENCES user_group (id)');
        $this->addSql('ALTER TABLE person ADD organization_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE person ADD CONSTRAINT FK_34DCD17632C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('CREATE INDEX IDX_34DCD17632C8A3DE ON person (organization_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE member_group DROP FOREIGN KEY FK_FE1D1367597D3FE');
        $this->addSql('ALTER TABLE member_group DROP FOREIGN KEY FK_FE1D136FE54D947');
        $this->addSql('DROP TABLE member_group');
        $this->addSql('ALTER TABLE `person` DROP FOREIGN KEY FK_34DCD17632C8A3DE');
        $this->addSql('DROP INDEX IDX_34DCD17632C8A3DE ON `person`');
        $this->addSql('ALTER TABLE `person` DROP organization_id');
    }
}
