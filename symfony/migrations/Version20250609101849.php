<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250609101849 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE module_competence_assignment ADD diploma_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module_competence_assignment ADD CONSTRAINT FK_ACBAC608A99ACEB5 FOREIGN KEY (diploma_id) REFERENCES diploma (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_ACBAC608A99ACEB5 ON module_competence_assignment (diploma_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module_competence_assignment DROP CONSTRAINT FK_ACBAC608A99ACEB5
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_ACBAC608A99ACEB5
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module_competence_assignment DROP diploma_id
        SQL);
    }
}
