<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250612123154 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE assessment (id UUID NOT NULL, school_id UUID DEFAULT NULL, name VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_F7523D70C32A47EE ON assessment (school_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN assessment.id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN assessment.school_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE assignment (id UUID NOT NULL, program_id UUID DEFAULT NULL, module_id UUID DEFAULT NULL, part INT NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_30C544BA3EB8070A ON assignment (program_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_30C544BAAFC2B591 ON assignment (module_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN assignment.id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN assignment.program_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN assignment.module_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE category (id UUID NOT NULL, school_id UUID DEFAULT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_64C19C1C32A47EE ON category (school_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN category.id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN category.school_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE competence (id UUID NOT NULL, diploma_id UUID DEFAULT NULL, title VARCHAR(255) NOT NULL, rncp VARCHAR(255) NOT NULL, content TEXT NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_94D4687FA99ACEB5 ON competence (diploma_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN competence.id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN competence.diploma_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE credit (id UUID NOT NULL, school_id UUID DEFAULT NULL, query VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_1CC16EFEC32A47EE ON credit (school_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN credit.id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN credit.school_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN credit.created_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE diploma (id UUID NOT NULL, school_id UUID DEFAULT NULL, title VARCHAR(255) NOT NULL, rncp VARCHAR(255) NOT NULL, content TEXT NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_EC218957C32A47EE ON diploma (school_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN diploma.id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN diploma.school_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE invoice (id UUID NOT NULL, subscription_id UUID DEFAULT NULL, stripe_invoice_id VARCHAR(255) NOT NULL, amount INT NOT NULL, currency VARCHAR(10) NOT NULL, paid_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, invoice_url VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_9065174452875775 ON invoice (stripe_invoice_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_906517449A1887DC ON invoice (subscription_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN invoice.id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN invoice.subscription_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE module (id UUID NOT NULL, owner_id UUID NOT NULL, title VARCHAR(255) NOT NULL, duration INT DEFAULT NULL, credit INT DEFAULT NULL, goal TEXT DEFAULT NULL, syllabus TEXT DEFAULT NULL, comment TEXT DEFAULT NULL, is_archived BOOLEAN DEFAULT NULL, is_shared BOOLEAN DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_C2426287E3C61F9 ON module (owner_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN module.id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN module.owner_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE module_category (module_id UUID NOT NULL, category_id UUID NOT NULL, PRIMARY KEY(module_id, category_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_628CCA3FAFC2B591 ON module_category (module_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_628CCA3F12469DE2 ON module_category (category_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN module_category.module_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN module_category.category_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE module_assessment (module_id UUID NOT NULL, assessment_id UUID NOT NULL, PRIMARY KEY(module_id, assessment_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_62B716EDAFC2B591 ON module_assessment (module_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_62B716EDDD3DD5F1 ON module_assessment (assessment_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN module_assessment.module_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN module_assessment.assessment_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE module_competence_assignment (id UUID NOT NULL, program_id UUID DEFAULT NULL, competence_id UUID DEFAULT NULL, module_id UUID DEFAULT NULL, diploma_id UUID DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_ACBAC6083EB8070A ON module_competence_assignment (program_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_ACBAC60815761DAB ON module_competence_assignment (competence_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_ACBAC608AFC2B591 ON module_competence_assignment (module_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_ACBAC608A99ACEB5 ON module_competence_assignment (diploma_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN module_competence_assignment.id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN module_competence_assignment.program_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN module_competence_assignment.competence_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN module_competence_assignment.module_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN module_competence_assignment.diploma_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE program (id UUID NOT NULL, owner_id UUID DEFAULT NULL, title VARCHAR(255) NOT NULL, year INT NOT NULL, prerequisites TEXT DEFAULT NULL, goals TEXT DEFAULT NULL, notes TEXT DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_92ED77847E3C61F9 ON program (owner_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN program.id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN program.owner_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE program_diploma (program_id UUID NOT NULL, diploma_id UUID NOT NULL, PRIMARY KEY(program_id, diploma_id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_67CB71833EB8070A ON program_diploma (program_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_67CB7183A99ACEB5 ON program_diploma (diploma_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN program_diploma.program_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN program_diploma.diploma_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE reset_password_request (id UUID NOT NULL, user_id UUID NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_7CE748AA76ED395 ON reset_password_request (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN reset_password_request.id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN reset_password_request.user_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN reset_password_request.requested_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN reset_password_request.expires_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE school (id UUID NOT NULL, name VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, img VARCHAR(255) DEFAULT NULL, primary_color VARCHAR(255) DEFAULT NULL, secondary_color VARCHAR(255) DEFAULT NULL, typo VARCHAR(255) DEFAULT NULL, limit_users INT DEFAULT NULL, is_free_account BOOLEAN DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN school.id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE setting (id UUID NOT NULL, name VARCHAR(255) NOT NULL, value TEXT DEFAULT NULL, type VARCHAR(50) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_9F74B8985E237E06 ON setting (name)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN setting.id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE subscription (id UUID NOT NULL, school_id UUID DEFAULT NULL, stripe_subscription_id VARCHAR(255) NOT NULL, limit_users INT DEFAULT NULL, stripe_customer_id VARCHAR(255) NOT NULL, is_unsubscribed BOOLEAN DEFAULT NULL, chatgpt BOOLEAN DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, canceled_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_A3C664D3C32A47EE ON subscription (school_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN subscription.id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN subscription.school_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN subscription.created_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN subscription.updated_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN subscription.canceled_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE "user" (id UUID NOT NULL, school_id UUID DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, is_verified BOOLEAN NOT NULL, firstname VARCHAR(255) DEFAULT NULL, lastname VARCHAR(255) DEFAULT NULL, country VARCHAR(255) DEFAULT NULL, is_disable BOOLEAN DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_8D93D649C32A47EE ON "user" (school_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON "user" (email)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN "user".id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN "user".school_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN messenger_messages.created_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN messenger_messages.available_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN messenger_messages.delivered_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
                BEGIN
                    PERFORM pg_notify('messenger_messages', NEW.queue_name::text);
                    RETURN NEW;
                END;
            $$ LANGUAGE plpgsql;
        SQL);
        $this->addSql(<<<'SQL'
            DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE assessment ADD CONSTRAINT FK_F7523D70C32A47EE FOREIGN KEY (school_id) REFERENCES school (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE assignment ADD CONSTRAINT FK_30C544BA3EB8070A FOREIGN KEY (program_id) REFERENCES program (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE assignment ADD CONSTRAINT FK_30C544BAAFC2B591 FOREIGN KEY (module_id) REFERENCES module (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE category ADD CONSTRAINT FK_64C19C1C32A47EE FOREIGN KEY (school_id) REFERENCES school (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE competence ADD CONSTRAINT FK_94D4687FA99ACEB5 FOREIGN KEY (diploma_id) REFERENCES diploma (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE credit ADD CONSTRAINT FK_1CC16EFEC32A47EE FOREIGN KEY (school_id) REFERENCES school (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE diploma ADD CONSTRAINT FK_EC218957C32A47EE FOREIGN KEY (school_id) REFERENCES school (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice ADD CONSTRAINT FK_906517449A1887DC FOREIGN KEY (subscription_id) REFERENCES subscription (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module ADD CONSTRAINT FK_C2426287E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module_category ADD CONSTRAINT FK_628CCA3FAFC2B591 FOREIGN KEY (module_id) REFERENCES module (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module_category ADD CONSTRAINT FK_628CCA3F12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module_assessment ADD CONSTRAINT FK_62B716EDAFC2B591 FOREIGN KEY (module_id) REFERENCES module (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module_assessment ADD CONSTRAINT FK_62B716EDDD3DD5F1 FOREIGN KEY (assessment_id) REFERENCES assessment (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module_competence_assignment ADD CONSTRAINT FK_ACBAC6083EB8070A FOREIGN KEY (program_id) REFERENCES program (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module_competence_assignment ADD CONSTRAINT FK_ACBAC60815761DAB FOREIGN KEY (competence_id) REFERENCES competence (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module_competence_assignment ADD CONSTRAINT FK_ACBAC608AFC2B591 FOREIGN KEY (module_id) REFERENCES module (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module_competence_assignment ADD CONSTRAINT FK_ACBAC608A99ACEB5 FOREIGN KEY (diploma_id) REFERENCES diploma (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE program ADD CONSTRAINT FK_92ED77847E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE program_diploma ADD CONSTRAINT FK_67CB71833EB8070A FOREIGN KEY (program_id) REFERENCES program (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE program_diploma ADD CONSTRAINT FK_67CB7183A99ACEB5 FOREIGN KEY (diploma_id) REFERENCES diploma (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3C32A47EE FOREIGN KEY (school_id) REFERENCES school (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "user" ADD CONSTRAINT FK_8D93D649C32A47EE FOREIGN KEY (school_id) REFERENCES school (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE assessment DROP CONSTRAINT FK_F7523D70C32A47EE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE assignment DROP CONSTRAINT FK_30C544BA3EB8070A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE assignment DROP CONSTRAINT FK_30C544BAAFC2B591
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE category DROP CONSTRAINT FK_64C19C1C32A47EE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE competence DROP CONSTRAINT FK_94D4687FA99ACEB5
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE credit DROP CONSTRAINT FK_1CC16EFEC32A47EE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE diploma DROP CONSTRAINT FK_EC218957C32A47EE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE invoice DROP CONSTRAINT FK_906517449A1887DC
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module DROP CONSTRAINT FK_C2426287E3C61F9
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module_category DROP CONSTRAINT FK_628CCA3FAFC2B591
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module_category DROP CONSTRAINT FK_628CCA3F12469DE2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module_assessment DROP CONSTRAINT FK_62B716EDAFC2B591
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module_assessment DROP CONSTRAINT FK_62B716EDDD3DD5F1
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module_competence_assignment DROP CONSTRAINT FK_ACBAC6083EB8070A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module_competence_assignment DROP CONSTRAINT FK_ACBAC60815761DAB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module_competence_assignment DROP CONSTRAINT FK_ACBAC608AFC2B591
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE module_competence_assignment DROP CONSTRAINT FK_ACBAC608A99ACEB5
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE program DROP CONSTRAINT FK_92ED77847E3C61F9
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE program_diploma DROP CONSTRAINT FK_67CB71833EB8070A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE program_diploma DROP CONSTRAINT FK_67CB7183A99ACEB5
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reset_password_request DROP CONSTRAINT FK_7CE748AA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE subscription DROP CONSTRAINT FK_A3C664D3C32A47EE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE "user" DROP CONSTRAINT FK_8D93D649C32A47EE
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE assessment
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE assignment
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE category
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE competence
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE credit
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE diploma
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE invoice
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE module
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE module_category
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE module_assessment
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE module_competence_assignment
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE program
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE program_diploma
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE reset_password_request
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE school
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE setting
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE subscription
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE "user"
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE messenger_messages
        SQL);
    }
}
