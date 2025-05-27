<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250428181147 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP SEQUENCE program_id_seq CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            DROP SEQUENCE user_program_school_id_seq CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user_school (id SERIAL NOT NULL, user_id INT NOT NULL, school_id INT NOT NULL, PRIMARY KEY(id))
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
        $this->addSql(<<<'SQL'
            ALTER TABLE user_program_school DROP CONSTRAINT fk_c65b3bb8a76ed395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_program_school DROP CONSTRAINT fk_c65b3bb83eb8070a
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_program_school DROP CONSTRAINT fk_c65b3bb8c32a47ee
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE program DROP CONSTRAINT fk_92ed77847e3c61f9
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_program_school
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE program
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE subscription DROP limit_programs
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "user" DROP limit_programs
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            CREATE SEQUENCE program_id_seq INCREMENT BY 1 MINVALUE 1 START 1
        SQL);
        $this->addSql(<<<'SQL'
            CREATE SEQUENCE user_program_school_id_seq INCREMENT BY 1 MINVALUE 1 START 1
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user_program_school (id SERIAL NOT NULL, user_id INT NOT NULL, program_id INT NOT NULL, school_id INT NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_c65b3bb8c32a47ee ON user_program_school (school_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_c65b3bb83eb8070a ON user_program_school (program_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_c65b3bb8a76ed395 ON user_program_school (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE program (id SERIAL NOT NULL, owner_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, is_disable BOOLEAN DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_92ed77847e3c61f9 ON program (owner_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_program_school ADD CONSTRAINT fk_c65b3bb8a76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_program_school ADD CONSTRAINT fk_c65b3bb83eb8070a FOREIGN KEY (program_id) REFERENCES program (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_program_school ADD CONSTRAINT fk_c65b3bb8c32a47ee FOREIGN KEY (school_id) REFERENCES school (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE program ADD CONSTRAINT fk_92ed77847e3c61f9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
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
        $this->addSql(<<<'SQL'
            ALTER TABLE "user" ADD limit_programs INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE subscription ADD limit_programs INT DEFAULT NULL
        SQL);
    }
}
