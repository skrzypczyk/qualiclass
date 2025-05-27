<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250522120021 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE category_module (category_id INT NOT NULL, module_id INT NOT NULL, PRIMARY KEY(category_id, module_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_3CCEC5312469DE2 ON category_module (category_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_3CCEC53AFC2B591 ON category_module (module_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE category_module ADD CONSTRAINT FK_3CCEC5312469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE category_module ADD CONSTRAINT FK_3CCEC53AFC2B591 FOREIGN KEY (module_id) REFERENCES module (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE category_module DROP CONSTRAINT FK_3CCEC5312469DE2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE category_module DROP CONSTRAINT FK_3CCEC53AFC2B591
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE category_module
        SQL);
    }
}
