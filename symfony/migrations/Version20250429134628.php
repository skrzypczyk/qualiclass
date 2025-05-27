<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250429134628 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE module ADD duration INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module ADD credit INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module ADD goal TEXT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module ADD syllabus TEXT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module ADD assessment TEXT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module ADD comment TEXT DEFAULT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module DROP duration
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module DROP credit
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module DROP goal
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module DROP syllabus
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module DROP assessment
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module DROP comment
        SQL);
    }
}
