<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250525160852 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE program (id SERIAL NOT NULL, title VARCHAR(255) NOT NULL, year INT NOT NULL, prerequisites TEXT NOT NULL, goals TEXT NOT NULL, notes TEXT NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE program_diploma (program_id INT NOT NULL, diploma_id INT NOT NULL, PRIMARY KEY(program_id, diploma_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_67CB71833EB8070A ON program_diploma (program_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_67CB7183A99ACEB5 ON program_diploma (diploma_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE program_diploma ADD CONSTRAINT FK_67CB71833EB8070A FOREIGN KEY (program_id) REFERENCES program (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE program_diploma ADD CONSTRAINT FK_67CB7183A99ACEB5 FOREIGN KEY (diploma_id) REFERENCES diploma (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE program_diploma DROP CONSTRAINT FK_67CB71833EB8070A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE program_diploma DROP CONSTRAINT FK_67CB7183A99ACEB5
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE program
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE program_diploma
        SQL);
    }
}
