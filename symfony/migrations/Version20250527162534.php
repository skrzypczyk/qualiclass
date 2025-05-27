<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250527162534 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE category DROP CONSTRAINT fk_64c19c1c32a47ee
        SQL);
        $this->addSql(<<<'SQL'
            DROP SEQUENCE school_id_seq CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE assessment_school DROP CONSTRAINT fk_d434bd5dd3dd5f1
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE assessment_school DROP CONSTRAINT fk_d434bd5c32a47ee
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE diploma_school DROP CONSTRAINT fk_bf685725a99aceb5
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE diploma_school DROP CONSTRAINT fk_bf685725c32a47ee
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE school DROP CONSTRAINT fk_f99edabb7e3c61f9
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_school DROP CONSTRAINT fk_9cccc186a76ed395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_school DROP CONSTRAINT fk_9cccc186c32a47ee
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE assessment_school
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE diploma_school
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE school
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_school
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_64c19c1c32a47ee
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE category DROP school_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE subscription DROP limit_schools
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "user" DROP limit_schools
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            CREATE SEQUENCE school_id_seq INCREMENT BY 1 MINVALUE 1 START 1
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE assessment_school (assessment_id INT NOT NULL, school_id INT NOT NULL, PRIMARY KEY(assessment_id, school_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_d434bd5c32a47ee ON assessment_school (school_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_d434bd5dd3dd5f1 ON assessment_school (assessment_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE diploma_school (diploma_id INT NOT NULL, school_id INT NOT NULL, PRIMARY KEY(diploma_id, school_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_bf685725c32a47ee ON diploma_school (school_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_bf685725a99aceb5 ON diploma_school (diploma_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE school (id SERIAL NOT NULL, owner_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, logo VARCHAR(255) DEFAULT NULL, is_disable BOOLEAN DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_f99edabb7e3c61f9 ON school (owner_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user_school (user_id INT NOT NULL, school_id INT NOT NULL, PRIMARY KEY(user_id, school_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_9cccc186c32a47ee ON user_school (school_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_9cccc186a76ed395 ON user_school (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE assessment_school ADD CONSTRAINT fk_d434bd5dd3dd5f1 FOREIGN KEY (assessment_id) REFERENCES assessment (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE assessment_school ADD CONSTRAINT fk_d434bd5c32a47ee FOREIGN KEY (school_id) REFERENCES school (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE diploma_school ADD CONSTRAINT fk_bf685725a99aceb5 FOREIGN KEY (diploma_id) REFERENCES diploma (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE diploma_school ADD CONSTRAINT fk_bf685725c32a47ee FOREIGN KEY (school_id) REFERENCES school (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE school ADD CONSTRAINT fk_f99edabb7e3c61f9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_school ADD CONSTRAINT fk_9cccc186a76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_school ADD CONSTRAINT fk_9cccc186c32a47ee FOREIGN KEY (school_id) REFERENCES school (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "user" ADD limit_schools INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE category ADD school_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE category ADD CONSTRAINT fk_64c19c1c32a47ee FOREIGN KEY (school_id) REFERENCES school (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_64c19c1c32a47ee ON category (school_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE subscription ADD limit_schools INT NOT NULL
        SQL);
    }
}
