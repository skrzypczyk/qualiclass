<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250525164036 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE program ALTER prerequisites DROP NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE program ALTER goals DROP NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE program ALTER notes DROP NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE program ALTER prerequisites SET NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE program ALTER goals SET NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE program ALTER notes SET NOT NULL
        SQL);
    }
}
