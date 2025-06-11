<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250609093350 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE module_competence_assignment (id SERIAL NOT NULL, program_id INT DEFAULT NULL, competence_id INT DEFAULT NULL, module_id INT DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_ACBAC6083EB8070A ON module_competence_assignment (program_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_ACBAC60815761DAB ON module_competence_assignment (competence_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_ACBAC608AFC2B591 ON module_competence_assignment (module_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module_competence_assignment ADD CONSTRAINT FK_ACBAC6083EB8070A FOREIGN KEY (program_id) REFERENCES program (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module_competence_assignment ADD CONSTRAINT FK_ACBAC60815761DAB FOREIGN KEY (competence_id) REFERENCES competence (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module_competence_assignment ADD CONSTRAINT FK_ACBAC608AFC2B591 FOREIGN KEY (module_id) REFERENCES module (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module_competence_assignment DROP CONSTRAINT FK_ACBAC6083EB8070A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module_competence_assignment DROP CONSTRAINT FK_ACBAC60815761DAB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module_competence_assignment DROP CONSTRAINT FK_ACBAC608AFC2B591
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE module_competence_assignment
        SQL);
    }
}
