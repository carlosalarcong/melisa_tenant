<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251210193006 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE group_permission (id INT AUTO_INCREMENT NOT NULL, group_id INT NOT NULL, created_by_id INT DEFAULT NULL, domain VARCHAR(100) NOT NULL, resource_id INT DEFAULT NULL, field_name VARCHAR(100) DEFAULT NULL, can_view TINYINT(1) NOT NULL, can_edit TINYINT(1) NOT NULL, can_delete TINYINT(1) NOT NULL, constraints JSON DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_3784F318FE54D947 (group_id), INDEX IDX_3784F318B03A8386 (created_by_id), INDEX idx_group_permission_domain (domain), INDEX idx_group_permission_resource (domain, resource_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE member_group_membership (member_id INT NOT NULL, member_group_id INT NOT NULL, INDEX IDX_61D5C0D17597D3FE (member_id), INDEX IDX_61D5C0D19897AAD (member_group_id), PRIMARY KEY(member_id, member_group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE member_group (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(150) NOT NULL, code VARCHAR(50) NOT NULL, description VARCHAR(500) DEFAULT NULL, active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_FE1D13677153098 (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE permission (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, created_by_id INT DEFAULT NULL, domain VARCHAR(100) NOT NULL, resource_id INT DEFAULT NULL, field_name VARCHAR(100) DEFAULT NULL, can_view TINYINT(1) NOT NULL, can_edit TINYINT(1) NOT NULL, can_delete TINYINT(1) NOT NULL, constraints JSON DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_E04992AAA76ED395 (user_id), INDEX IDX_E04992AAB03A8386 (created_by_id), INDEX IDX_E04992AAA76ED395A7A91E0B (user_id, domain), INDEX IDX_E04992AAA7A91E0B89329D25 (domain, resource_id), UNIQUE INDEX unique_user_permission (user_id, domain, resource_id, field_name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE group_permission ADD CONSTRAINT FK_3784F318FE54D947 FOREIGN KEY (group_id) REFERENCES member_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE group_permission ADD CONSTRAINT FK_3784F318B03A8386 FOREIGN KEY (created_by_id) REFERENCES `member` (id)');
        $this->addSql('ALTER TABLE member_group_membership ADD CONSTRAINT FK_61D5C0D17597D3FE FOREIGN KEY (member_id) REFERENCES `member` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE member_group_membership ADD CONSTRAINT FK_61D5C0D19897AAD FOREIGN KEY (member_group_id) REFERENCES member_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE permission ADD CONSTRAINT FK_E04992AAA76ED395 FOREIGN KEY (user_id) REFERENCES `member` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE permission ADD CONSTRAINT FK_E04992AAB03A8386 FOREIGN KEY (created_by_id) REFERENCES `member` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE group_permission DROP FOREIGN KEY FK_3784F318FE54D947');
        $this->addSql('ALTER TABLE group_permission DROP FOREIGN KEY FK_3784F318B03A8386');
        $this->addSql('ALTER TABLE member_group_membership DROP FOREIGN KEY FK_61D5C0D17597D3FE');
        $this->addSql('ALTER TABLE member_group_membership DROP FOREIGN KEY FK_61D5C0D19897AAD');
        $this->addSql('ALTER TABLE permission DROP FOREIGN KEY FK_E04992AAA76ED395');
        $this->addSql('ALTER TABLE permission DROP FOREIGN KEY FK_E04992AAB03A8386');
        $this->addSql('DROP TABLE group_permission');
        $this->addSql('DROP TABLE member_group_membership');
        $this->addSql('DROP TABLE member_group');
        $this->addSql('DROP TABLE permission');
    }
}
