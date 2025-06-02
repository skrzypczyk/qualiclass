<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250602075605 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE subscription DROP CONSTRAINT fk_a3c664d37e3c61f9
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_a3c664d37e3c61f9
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE subscription RENAME COLUMN owner_id TO school_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3C32A47EE FOREIGN KEY (school_id) REFERENCES school (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_A3C664D3C32A47EE ON subscription (school_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "user" DROP stripe_subscription_id
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE subscription DROP CONSTRAINT FK_A3C664D3C32A47EE
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_A3C664D3C32A47EE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE subscription RENAME COLUMN school_id TO owner_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE subscription ADD CONSTRAINT fk_a3c664d37e3c61f9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_a3c664d37e3c61f9 ON subscription (owner_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "user" ADD stripe_subscription_id VARCHAR(255) DEFAULT NULL
        SQL);
    }
}
