<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250609084428 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP SEQUENCE affectation_id_seq CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE assignment (id SERIAL NOT NULL, program_id INT DEFAULT NULL, module_id INT DEFAULT NULL, part INT NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_30C544BA3EB8070A ON assignment (program_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_30C544BAAFC2B591 ON assignment (module_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE assignment ADD CONSTRAINT FK_30C544BA3EB8070A FOREIGN KEY (program_id) REFERENCES program (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE assignment ADD CONSTRAINT FK_30C544BAAFC2B591 FOREIGN KEY (module_id) REFERENCES module (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE affectation DROP CONSTRAINT fk_f4dd61d33eb8070a
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE affectation DROP CONSTRAINT fk_f4dd61d3afc2b591
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE affectation
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            CREATE SEQUENCE affectation_id_seq INCREMENT BY 1 MINVALUE 1 START 1
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE affectation (id SERIAL NOT NULL, program_id INT DEFAULT NULL, module_id INT DEFAULT NULL, part INT NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_f4dd61d3afc2b591 ON affectation (module_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_f4dd61d33eb8070a ON affectation (program_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE affectation ADD CONSTRAINT fk_f4dd61d33eb8070a FOREIGN KEY (program_id) REFERENCES program (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE affectation ADD CONSTRAINT fk_f4dd61d3afc2b591 FOREIGN KEY (module_id) REFERENCES module (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE assignment DROP CONSTRAINT FK_30C544BA3EB8070A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE assignment DROP CONSTRAINT FK_30C544BAAFC2B591
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE assignment
        SQL);
    }
}
