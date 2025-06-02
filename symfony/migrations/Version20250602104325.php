<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250602104325 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE diploma DROP CONSTRAINT fk_ec2189577e3c61f9
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_ec2189577e3c61f9
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE diploma ADD school_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE diploma DROP owner_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE diploma ADD CONSTRAINT FK_EC218957C32A47EE FOREIGN KEY (school_id) REFERENCES school (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_EC218957C32A47EE ON diploma (school_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE diploma DROP CONSTRAINT FK_EC218957C32A47EE
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_EC218957C32A47EE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE diploma ADD owner_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE diploma DROP school_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE diploma ADD CONSTRAINT fk_ec2189577e3c61f9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_ec2189577e3c61f9 ON diploma (owner_id)
        SQL);
    }
}
