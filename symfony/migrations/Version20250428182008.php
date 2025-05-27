<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250428182008 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP SEQUENCE user_school_id_seq CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_school DROP CONSTRAINT fk_9cccc186a76ed395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_school DROP CONSTRAINT fk_9cccc186c32a47ee
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_school
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            CREATE SEQUENCE user_school_id_seq INCREMENT BY 1 MINVALUE 1 START 1
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user_school (id SERIAL NOT NULL, user_id INT NOT NULL, school_id INT NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_9cccc186c32a47ee ON user_school (school_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_9cccc186a76ed395 ON user_school (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_school ADD CONSTRAINT fk_9cccc186a76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_school ADD CONSTRAINT fk_9cccc186c32a47ee FOREIGN KEY (school_id) REFERENCES school (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }
}
