<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250420180635 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE subscription (id SERIAL NOT NULL, owner_id INT DEFAULT NULL, stripe_subscription_id VARCHAR(255) NOT NULL, limit_school INT NOT NULL, limit_user INT DEFAULT NULL, limit_program INT DEFAULT NULL, stripe_customer_id VARCHAR(255) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_A3C664D37E3C61F9 ON subscription (owner_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D37E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice DROP CONSTRAINT fk_906517447e3c61f9
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_906517447e3c61f9
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice DROP stripe_subscription_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice DROP stripe_customer_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice DROP schools
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice DROP users
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice DROP programs
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice RENAME COLUMN owner_id TO subscription_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice ADD CONSTRAINT FK_906517449A1887DC FOREIGN KEY (subscription_id) REFERENCES subscription (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_906517449A1887DC ON invoice (subscription_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice DROP CONSTRAINT FK_906517449A1887DC
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE subscription DROP CONSTRAINT FK_A3C664D37E3C61F9
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE subscription
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_906517449A1887DC
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice ADD stripe_subscription_id VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice ADD stripe_customer_id VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice ADD schools INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice ADD users INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice ADD programs INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice RENAME COLUMN subscription_id TO owner_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice ADD CONSTRAINT fk_906517447e3c61f9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_906517447e3c61f9 ON invoice (owner_id)
        SQL);
    }
}
