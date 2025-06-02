<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250602100937 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE assessment DROP CONSTRAINT fk_f7523d707e3c61f9
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_f7523d707e3c61f9
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE assessment RENAME COLUMN owner_id TO school_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE assessment ADD CONSTRAINT FK_F7523D70C32A47EE FOREIGN KEY (school_id) REFERENCES school (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_F7523D70C32A47EE ON assessment (school_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE assessment DROP CONSTRAINT FK_F7523D70C32A47EE
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_F7523D70C32A47EE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE assessment RENAME COLUMN school_id TO owner_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE assessment ADD CONSTRAINT fk_f7523d707e3c61f9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_f7523d707e3c61f9 ON assessment (owner_id)
        SQL);
    }
}
