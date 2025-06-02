<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250602090146 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE school ADD limit_users INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE school ADD is_free_account BOOLEAN DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "user" DROP is_free_account
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "user" DROP limit_users
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "user" ADD is_free_account BOOLEAN DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "user" ADD limit_users INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE school DROP limit_users
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE school DROP is_free_account
        SQL);
    }
}
