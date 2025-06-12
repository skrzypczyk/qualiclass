<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250612082238 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {

        $this->addSql('CREATE EXTENSION IF NOT EXISTS "uuid-ossp";');
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE assessment ADD uuid UUID DEFAULT gen_random_uuid() NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN assessment.uuid IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_F7523D70D17F50A6 ON assessment (uuid)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE assignment ADD uuid UUID DEFAULT gen_random_uuid() NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN assignment.uuid IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_30C544BAD17F50A6 ON assignment (uuid)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE category ADD uuid UUID DEFAULT gen_random_uuid() NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN category.uuid IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_64C19C1D17F50A6 ON category (uuid)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE competence ADD uuid UUID DEFAULT gen_random_uuid() NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN competence.uuid IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_94D4687FD17F50A6 ON competence (uuid)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE credit ADD uuid UUID DEFAULT gen_random_uuid() NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN credit.uuid IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_1CC16EFED17F50A6 ON credit (uuid)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE diploma ADD uuid UUID DEFAULT gen_random_uuid() NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN diploma.uuid IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_EC218957D17F50A6 ON diploma (uuid)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice ADD uuid UUID DEFAULT gen_random_uuid() NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN invoice.uuid IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_90651744D17F50A6 ON invoice (uuid)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module ADD uuid UUID DEFAULT gen_random_uuid() NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN module.uuid IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_C242628D17F50A6 ON module (uuid)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module_competence_assignment ADD uuid UUID DEFAULT gen_random_uuid() NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN module_competence_assignment.uuid IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_ACBAC608D17F50A6 ON module_competence_assignment (uuid)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE program ADD uuid UUID DEFAULT gen_random_uuid() NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN program.uuid IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_92ED7784D17F50A6 ON program (uuid)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reset_password_request ADD uuid UUID DEFAULT gen_random_uuid() NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN reset_password_request.uuid IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_7CE748AD17F50A6 ON reset_password_request (uuid)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE school ADD uuid UUID DEFAULT gen_random_uuid() NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN school.uuid IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_F99EDABBD17F50A6 ON school (uuid)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE setting ADD uuid UUID DEFAULT gen_random_uuid() NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN setting.uuid IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_9F74B898D17F50A6 ON setting (uuid)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE subscription ADD uuid UUID DEFAULT gen_random_uuid() NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN subscription.uuid IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_A3C664D3D17F50A6 ON subscription (uuid)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "user" ADD uuid UUID DEFAULT gen_random_uuid() NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN "user".uuid IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_8D93D649D17F50A6 ON "user" (uuid)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_F99EDABBD17F50A6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE school DROP uuid
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_92ED7784D17F50A6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE program DROP uuid
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_A3C664D3D17F50A6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE subscription DROP uuid
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_30C544BAD17F50A6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE assignment DROP uuid
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_EC218957D17F50A6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE diploma DROP uuid
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_ACBAC608D17F50A6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module_competence_assignment DROP uuid
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_8D93D649D17F50A6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "user" DROP uuid
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_9F74B898D17F50A6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE setting DROP uuid
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_7CE748AD17F50A6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reset_password_request DROP uuid
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_90651744D17F50A6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice DROP uuid
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_1CC16EFED17F50A6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE credit DROP uuid
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_C242628D17F50A6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module DROP uuid
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_94D4687FD17F50A6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE competence DROP uuid
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_64C19C1D17F50A6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE category DROP uuid
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_F7523D70D17F50A6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE assessment DROP uuid
        SQL);
    }
}
