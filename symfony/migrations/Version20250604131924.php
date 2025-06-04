<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250604131924 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE module_assessment (module_id INT NOT NULL, assessment_id INT NOT NULL, PRIMARY KEY(module_id, assessment_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_62B716EDAFC2B591 ON module_assessment (module_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_62B716EDDD3DD5F1 ON module_assessment (assessment_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module_assessment ADD CONSTRAINT FK_62B716EDAFC2B591 FOREIGN KEY (module_id) REFERENCES module (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module_assessment ADD CONSTRAINT FK_62B716EDDD3DD5F1 FOREIGN KEY (assessment_id) REFERENCES assessment (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module_assessment DROP CONSTRAINT FK_62B716EDAFC2B591
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module_assessment DROP CONSTRAINT FK_62B716EDDD3DD5F1
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE module_assessment
        SQL);
    }
}
