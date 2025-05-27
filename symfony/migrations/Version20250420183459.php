<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250420183459 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE subscription ADD limit_users INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE subscription ADD limit_programs INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE subscription DROP limit_user
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE subscription DROP limit_program
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE subscription RENAME COLUMN limit_school TO limit_schools
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE subscription ADD limit_user INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE subscription ADD limit_program INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE subscription DROP limit_users
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE subscription DROP limit_programs
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE subscription RENAME COLUMN limit_schools TO limit_school
        SQL);
    }
}
