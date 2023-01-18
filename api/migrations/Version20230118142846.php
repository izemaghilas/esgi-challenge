<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230118142846 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE category_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE comment_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE content_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE forgot_password_token_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE register_token_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE reported_content_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE subscription_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE validation_request_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE category (id INT NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE comment (id INT NOT NULL, commenter_id_id INT NOT NULL, content VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9474526C455BE0B1 ON comment (commenter_id_id)');
        $this->addSql('CREATE TABLE content (id INT NOT NULL, creator_id_id INT NOT NULL, category_id_id INT NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, media_link VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, thumbnail VARCHAR(255) NOT NULL, active BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FEC530A9F05788E9 ON content (creator_id_id)');
        $this->addSql('CREATE INDEX IDX_FEC530A99777D11E ON content (category_id_id)');
        $this->addSql('COMMENT ON COLUMN content.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE forgot_password_token (id INT NOT NULL, user_id_id INT NOT NULL, token VARCHAR(255) NOT NULL, expire_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8625E4A29D86650F ON forgot_password_token (user_id_id)');
        $this->addSql('COMMENT ON COLUMN forgot_password_token.expire_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE register_token (id INT NOT NULL, user_id_id INT NOT NULL, token VARCHAR(255) NOT NULL, expire_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_893565C39D86650F ON register_token (user_id_id)');
        $this->addSql('COMMENT ON COLUMN register_token.expire_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE reported_content (id INT NOT NULL, content_id_id INT NOT NULL, reporter_id_id INT NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_594CB5529487CA85 ON reported_content (content_id_id)');
        $this->addSql('CREATE INDEX IDX_594CB552D6B1FFA1 ON reported_content (reporter_id_id)');
        $this->addSql('CREATE TABLE subscription (id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expire_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN subscription.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN subscription.expire_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, subscription_id_id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, phone_number VARCHAR(15) DEFAULT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, role JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649857C9F24 ON "user" (subscription_id_id)');
        $this->addSql('COMMENT ON COLUMN "user".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE validation_request (id INT NOT NULL, reviewer_id_id INT NOT NULL, content_id_id INT NOT NULL, active BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2A12D2912DC63656 ON validation_request (reviewer_id_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2A12D2919487CA85 ON validation_request (content_id_id)');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C455BE0B1 FOREIGN KEY (commenter_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE content ADD CONSTRAINT FK_FEC530A9F05788E9 FOREIGN KEY (creator_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE content ADD CONSTRAINT FK_FEC530A99777D11E FOREIGN KEY (category_id_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE forgot_password_token ADD CONSTRAINT FK_8625E4A29D86650F FOREIGN KEY (user_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE register_token ADD CONSTRAINT FK_893565C39D86650F FOREIGN KEY (user_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reported_content ADD CONSTRAINT FK_594CB5529487CA85 FOREIGN KEY (content_id_id) REFERENCES content (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reported_content ADD CONSTRAINT FK_594CB552D6B1FFA1 FOREIGN KEY (reporter_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D649857C9F24 FOREIGN KEY (subscription_id_id) REFERENCES subscription (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE validation_request ADD CONSTRAINT FK_2A12D2912DC63656 FOREIGN KEY (reviewer_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE validation_request ADD CONSTRAINT FK_2A12D2919487CA85 FOREIGN KEY (content_id_id) REFERENCES content (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE category_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE comment_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE content_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE forgot_password_token_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE register_token_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE reported_content_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE subscription_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE "user_id_seq" CASCADE');
        $this->addSql('DROP SEQUENCE validation_request_id_seq CASCADE');
        $this->addSql('ALTER TABLE comment DROP CONSTRAINT FK_9474526C455BE0B1');
        $this->addSql('ALTER TABLE content DROP CONSTRAINT FK_FEC530A9F05788E9');
        $this->addSql('ALTER TABLE content DROP CONSTRAINT FK_FEC530A99777D11E');
        $this->addSql('ALTER TABLE forgot_password_token DROP CONSTRAINT FK_8625E4A29D86650F');
        $this->addSql('ALTER TABLE register_token DROP CONSTRAINT FK_893565C39D86650F');
        $this->addSql('ALTER TABLE reported_content DROP CONSTRAINT FK_594CB5529487CA85');
        $this->addSql('ALTER TABLE reported_content DROP CONSTRAINT FK_594CB552D6B1FFA1');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D649857C9F24');
        $this->addSql('ALTER TABLE validation_request DROP CONSTRAINT FK_2A12D2912DC63656');
        $this->addSql('ALTER TABLE validation_request DROP CONSTRAINT FK_2A12D2919487CA85');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE content');
        $this->addSql('DROP TABLE forgot_password_token');
        $this->addSql('DROP TABLE register_token');
        $this->addSql('DROP TABLE reported_content');
        $this->addSql('DROP TABLE subscription');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE validation_request');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
