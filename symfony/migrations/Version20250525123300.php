<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250525123300 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE diploma_school (diploma_id INT NOT NULL, school_id INT NOT NULL, PRIMARY KEY(diploma_id, school_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_BF685725A99ACEB5 ON diploma_school (diploma_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_BF685725C32A47EE ON diploma_school (school_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE diploma_school ADD CONSTRAINT FK_BF685725A99ACEB5 FOREIGN KEY (diploma_id) REFERENCES diploma (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE diploma_school ADD CONSTRAINT FK_BF685725C32A47EE FOREIGN KEY (school_id) REFERENCES school (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE diploma_school DROP CONSTRAINT FK_BF685725A99ACEB5
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE diploma_school DROP CONSTRAINT FK_BF685725C32A47EE
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE diploma_school
        SQL);
    }
}
