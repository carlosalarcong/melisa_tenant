<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration para agregar campo can_view_cash_register a member
 */
final class Version20251230163000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add can_view_cash_register column to member table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `member` ADD can_view_cash_register TINYINT(1) DEFAULT 0');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `member` DROP can_view_cash_register');
    }
}
