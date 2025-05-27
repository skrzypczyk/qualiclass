<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250429184643 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE assessment_school DROP CONSTRAINT fk_d434bd5dd3dd5f1
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE assessment_school DROP CONSTRAINT fk_d434bd5c32a47ee
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE assessment_school
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE assessment_school (assessment_id INT NOT NULL, school_id INT NOT NULL, PRIMARY KEY(assessment_id, school_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_d434bd5c32a47ee ON assessment_school (school_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_d434bd5dd3dd5f1 ON assessment_school (assessment_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE assessment_school ADD CONSTRAINT fk_d434bd5dd3dd5f1 FOREIGN KEY (assessment_id) REFERENCES assessment (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE assessment_school ADD CONSTRAINT fk_d434bd5c32a47ee FOREIGN KEY (school_id) REFERENCES school (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }
}
