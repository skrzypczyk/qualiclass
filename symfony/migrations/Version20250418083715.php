<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250418083715 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE invoice (id SERIAL NOT NULL, user_id INT NOT NULL, stripe_invoice_id VARCHAR(255) NOT NULL, stripe_subscription_id VARCHAR(255) NOT NULL, stripe_customer_id VARCHAR(255) NOT NULL, amount INT NOT NULL, currency VARCHAR(10) NOT NULL, paid_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, schools INT NOT NULL, users INT NOT NULL, programs INT NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_9065174452875775 ON invoice (stripe_invoice_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_90651744A76ED395 ON invoice (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice ADD CONSTRAINT FK_90651744A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice DROP CONSTRAINT FK_90651744A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE invoice
        SQL);
    }
}
