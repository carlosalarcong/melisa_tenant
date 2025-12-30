<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration para agregar relación Member -> Person
 */
final class Version20251230162500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add person_id column to member table';
    }

    public function up(Schema $schema): void
    {
        // Agregar columna person_id a la tabla member
        $this->addSql('ALTER TABLE `member` ADD person_id INT DEFAULT NULL');
        
        // Agregar índice
        $this->addSql('CREATE INDEX IDX_70E4FA78217BBB47 ON `member` (person_id)');
        
        // Agregar foreign key
        $this->addSql('ALTER TABLE `member` ADD CONSTRAINT FK_70E4FA78217BBB47 FOREIGN KEY (person_id) REFERENCES `person` (id)');
    }

    public function down(Schema $schema): void
    {
        // Eliminar foreign key
        $this->addSql('ALTER TABLE `member` DROP FOREIGN KEY FK_70E4FA78217BBB47');
        
        // Eliminar índice
        $this->addSql('DROP INDEX IDX_70E4FA78217BBB47 ON `member`');
        
        // Eliminar columna
        $this->addSql('ALTER TABLE `member` DROP person_id');
    }
}
