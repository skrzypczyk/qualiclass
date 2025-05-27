<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250525122756 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE category_school (category_id INT NOT NULL, school_id INT NOT NULL, PRIMARY KEY(category_id, school_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_F67610C012469DE2 ON category_school (category_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_F67610C0C32A47EE ON category_school (school_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE category_school ADD CONSTRAINT FK_F67610C012469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE category_school ADD CONSTRAINT FK_F67610C0C32A47EE FOREIGN KEY (school_id) REFERENCES school (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE category_school DROP CONSTRAINT FK_F67610C012469DE2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE category_school DROP CONSTRAINT FK_F67610C0C32A47EE
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE category_school
        SQL);
    }
}
