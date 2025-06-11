<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250608083136 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP SEQUENCE module_competence_affectation_id_seq CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE affectation (id SERIAL NOT NULL, program_id INT DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_F4DD61D33EB8070A ON affectation (program_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE affectation ADD CONSTRAINT FK_F4DD61D33EB8070A FOREIGN KEY (program_id) REFERENCES program (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module_competence_affectation DROP CONSTRAINT fk_d12eccf1afc2b591
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module_competence_affectation DROP CONSTRAINT fk_d12eccf13eb8070a
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module_competence_affectation DROP CONSTRAINT fk_d12eccf115761dab
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE module_competence_affectation
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            CREATE SEQUENCE module_competence_affectation_id_seq INCREMENT BY 1 MINVALUE 1 START 1
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE module_competence_affectation (id SERIAL NOT NULL, module_id INT DEFAULT NULL, competence_id INT DEFAULT NULL, program_id INT DEFAULT NULL, part INT DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_d12eccf13eb8070a ON module_competence_affectation (program_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_d12eccf115761dab ON module_competence_affectation (competence_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_d12eccf1afc2b591 ON module_competence_affectation (module_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module_competence_affectation ADD CONSTRAINT fk_d12eccf1afc2b591 FOREIGN KEY (module_id) REFERENCES module (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module_competence_affectation ADD CONSTRAINT fk_d12eccf13eb8070a FOREIGN KEY (program_id) REFERENCES program (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module_competence_affectation ADD CONSTRAINT fk_d12eccf115761dab FOREIGN KEY (competence_id) REFERENCES competence (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE affectation DROP CONSTRAINT FK_F4DD61D33EB8070A
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE affectation
        SQL);
    }
}
