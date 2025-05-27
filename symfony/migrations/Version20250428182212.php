<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250428182212 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE user_school (user_id INT NOT NULL, school_id INT NOT NULL, PRIMARY KEY(user_id, school_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_9CCCC186A76ED395 ON user_school (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_9CCCC186C32A47EE ON user_school (school_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_school ADD CONSTRAINT FK_9CCCC186A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_school ADD CONSTRAINT FK_9CCCC186C32A47EE FOREIGN KEY (school_id) REFERENCES school (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_school DROP CONSTRAINT FK_9CCCC186A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_school DROP CONSTRAINT FK_9CCCC186C32A47EE
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_school
        SQL);
    }
}
