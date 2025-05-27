<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250523072211 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE competence (id SERIAL NOT NULL, diploma_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, rncp VARCHAR(255) NOT NULL, content TEXT NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_94D4687FA99ACEB5 ON competence (diploma_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE diploma (id SERIAL NOT NULL, owner_id INT NOT NULL, title VARCHAR(255) NOT NULL, rncp VARCHAR(255) NOT NULL, content TEXT NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_EC2189577E3C61F9 ON diploma (owner_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE competence ADD CONSTRAINT FK_94D4687FA99ACEB5 FOREIGN KEY (diploma_id) REFERENCES diploma (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE diploma ADD CONSTRAINT FK_EC2189577E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE competence DROP CONSTRAINT FK_94D4687FA99ACEB5
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE diploma DROP CONSTRAINT FK_EC2189577E3C61F9
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE competence
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE diploma
        SQL);
    }
}
