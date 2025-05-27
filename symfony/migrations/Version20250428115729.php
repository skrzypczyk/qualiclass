<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250428115729 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE user_program_school (id SERIAL NOT NULL, user_id INT NOT NULL, program_id INT NOT NULL, school_id INT NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_C65B3BB8A76ED395 ON user_program_school (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_C65B3BB83EB8070A ON user_program_school (program_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_C65B3BB8C32A47EE ON user_program_school (school_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_program_school ADD CONSTRAINT FK_C65B3BB8A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_program_school ADD CONSTRAINT FK_C65B3BB83EB8070A FOREIGN KEY (program_id) REFERENCES program (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_program_school ADD CONSTRAINT FK_C65B3BB8C32A47EE FOREIGN KEY (school_id) REFERENCES school (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_program_school DROP CONSTRAINT FK_C65B3BB8A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_program_school DROP CONSTRAINT FK_C65B3BB83EB8070A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_program_school DROP CONSTRAINT FK_C65B3BB8C32A47EE
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_program_school
        SQL);
    }
}
