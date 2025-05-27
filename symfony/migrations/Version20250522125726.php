<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250522125726 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE category_module DROP CONSTRAINT fk_3ccec5312469de2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE category_module DROP CONSTRAINT fk_3ccec53afc2b591
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE category_module
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE category_module (category_id INT NOT NULL, module_id INT NOT NULL, PRIMARY KEY(category_id, module_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_3ccec53afc2b591 ON category_module (module_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_3ccec5312469de2 ON category_module (category_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE category_module ADD CONSTRAINT fk_3ccec5312469de2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE category_module ADD CONSTRAINT fk_3ccec53afc2b591 FOREIGN KEY (module_id) REFERENCES module (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }
}
