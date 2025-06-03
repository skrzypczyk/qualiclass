<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250603152547 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE module_competence_affectation DROP CONSTRAINT FK_D12ECCF115761DAB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module_competence_affectation ADD CONSTRAINT FK_D12ECCF115761DAB FOREIGN KEY (competence_id) REFERENCES competence (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module_competence_affectation DROP CONSTRAINT fk_d12eccf115761dab
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module_competence_affectation ADD CONSTRAINT fk_d12eccf115761dab FOREIGN KEY (competence_id) REFERENCES competence (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }
}
