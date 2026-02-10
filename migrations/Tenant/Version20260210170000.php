<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260210170000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Crear tabla admission_record para persistencia base de admision hospitalaria/urgencia';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE admission_record (
            id SERIAL NOT NULL,
            person_id INT NOT NULL,
            admission_type VARCHAR(30) NOT NULL,
            status VARCHAR(30) NOT NULL,
            payer_id INT DEFAULT NULL,
            agreement_id INT DEFAULT NULL,
            service_id INT DEFAULT NULL,
            bed_id INT DEFAULT NULL,
            triage VARCHAR(10) DEFAULT NULL,
            consultation_reason TEXT DEFAULT NULL,
            created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE INDEX IDX_ADMISSION_RECORD_PERSON ON admission_record (person_id)');
        $this->addSql('ALTER TABLE admission_record ADD CONSTRAINT FK_ADMISSION_RECORD_PERSON FOREIGN KEY (person_id) REFERENCES person (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE admission_record');
    }
}

