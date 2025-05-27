<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250525125927 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE category_school DROP CONSTRAINT fk_f67610c012469de2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE category_school DROP CONSTRAINT fk_f67610c0c32a47ee
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE category_school
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE category_school (category_id INT NOT NULL, school_id INT NOT NULL, PRIMARY KEY(category_id, school_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_f67610c0c32a47ee ON category_school (school_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_f67610c012469de2 ON category_school (category_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE category_school ADD CONSTRAINT fk_f67610c012469de2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE category_school ADD CONSTRAINT fk_f67610c0c32a47ee FOREIGN KEY (school_id) REFERENCES school (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }
}
