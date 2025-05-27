<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250525204430 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE module_competence_affectation (id SERIAL NOT NULL, module_id INT DEFAULT NULL, competence_id INT DEFAULT NULL, program_id INT DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D12ECCF1AFC2B591 ON module_competence_affectation (module_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D12ECCF115761DAB ON module_competence_affectation (competence_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_D12ECCF13EB8070A ON module_competence_affectation (program_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module_competence_affectation ADD CONSTRAINT FK_D12ECCF1AFC2B591 FOREIGN KEY (module_id) REFERENCES module (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module_competence_affectation ADD CONSTRAINT FK_D12ECCF115761DAB FOREIGN KEY (competence_id) REFERENCES competence (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module_competence_affectation ADD CONSTRAINT FK_D12ECCF13EB8070A FOREIGN KEY (program_id) REFERENCES program (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module_competence_affectation DROP CONSTRAINT FK_D12ECCF1AFC2B591
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module_competence_affectation DROP CONSTRAINT FK_D12ECCF115761DAB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module_competence_affectation DROP CONSTRAINT FK_D12ECCF13EB8070A
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE module_competence_affectation
        SQL);
    }
}
