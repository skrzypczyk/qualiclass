<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250522125756 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE module_category (module_id INT NOT NULL, category_id INT NOT NULL, PRIMARY KEY(module_id, category_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_628CCA3FAFC2B591 ON module_category (module_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_628CCA3F12469DE2 ON module_category (category_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module_category ADD CONSTRAINT FK_628CCA3FAFC2B591 FOREIGN KEY (module_id) REFERENCES module (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module_category ADD CONSTRAINT FK_628CCA3F12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module_category DROP CONSTRAINT FK_628CCA3FAFC2B591
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module_category DROP CONSTRAINT FK_628CCA3F12469DE2
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE module_category
        SQL);
    }
}
