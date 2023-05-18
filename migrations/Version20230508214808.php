<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230508214808 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE be_reviewer_application (id UUID NOT NULL, contributor_id UUID DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, status VARCHAR(255) NOT NULL, motivation TEXT NOT NULL, skills TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6ABCB1227A19A357 ON be_reviewer_application (contributor_id)');
        $this->addSql('COMMENT ON COLUMN be_reviewer_application.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN be_reviewer_application.contributor_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN be_reviewer_application.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE category (id UUID NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN category.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE comment (id UUID NOT NULL, commenter_id_id UUID NOT NULL, course_id UUID NOT NULL, content VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9474526C455BE0B1 ON comment (commenter_id_id)');
        $this->addSql('CREATE INDEX IDX_9474526C591CC992 ON comment (course_id)');
        $this->addSql('COMMENT ON COLUMN comment.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN comment.commenter_id_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN comment.course_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN comment.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE content (id UUID NOT NULL, creator_id_id UUID NOT NULL, category_id_id UUID NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, media_link VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, thumbnail VARCHAR(255) NOT NULL, active BOOLEAN NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, price DOUBLE PRECISION DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FEC530A9F05788E9 ON content (creator_id_id)');
        $this->addSql('CREATE INDEX IDX_FEC530A99777D11E ON content (category_id_id)');
        $this->addSql('COMMENT ON COLUMN content.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN content.creator_id_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN content.category_id_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN content.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN content.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE purchase (id UUID NOT NULL, buyer_id UUID NOT NULL, course_id UUID NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6117D13B6C755722 ON purchase (buyer_id)');
        $this->addSql('CREATE INDEX IDX_6117D13B591CC992 ON purchase (course_id)');
        $this->addSql('COMMENT ON COLUMN purchase.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN purchase.buyer_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN purchase.course_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE reported_content (id UUID NOT NULL, content_id_id UUID NOT NULL, reporter_id_id UUID NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_594CB5529487CA85 ON reported_content (content_id_id)');
        $this->addSql('CREATE INDEX IDX_594CB552D6B1FFA1 ON reported_content (reporter_id_id)');
        $this->addSql('COMMENT ON COLUMN reported_content.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN reported_content.content_id_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN reported_content.reporter_id_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE "user" (id UUID NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, phone_number VARCHAR(15) DEFAULT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, active BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('COMMENT ON COLUMN "user".id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "user".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE validation_request (id UUID NOT NULL, reviewer_id_id UUID NOT NULL, content_id_id UUID NOT NULL, active BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2A12D2912DC63656 ON validation_request (reviewer_id_id)');
        $this->addSql('CREATE INDEX IDX_2A12D2919487CA85 ON validation_request (content_id_id)');
        $this->addSql('COMMENT ON COLUMN validation_request.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN validation_request.reviewer_id_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN validation_request.content_id_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN validation_request.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN validation_request.updated_at IS \'(DC2Type:datetime_immutable)\'');
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
        $this->addSql('ALTER TABLE be_reviewer_application ADD CONSTRAINT FK_6ABCB1227A19A357 FOREIGN KEY (contributor_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C455BE0B1 FOREIGN KEY (commenter_id_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C591CC992 FOREIGN KEY (course_id) REFERENCES content (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE content ADD CONSTRAINT FK_FEC530A9F05788E9 FOREIGN KEY (creator_id_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE content ADD CONSTRAINT FK_FEC530A99777D11E FOREIGN KEY (category_id_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE purchase ADD CONSTRAINT FK_6117D13B6C755722 FOREIGN KEY (buyer_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE purchase ADD CONSTRAINT FK_6117D13B591CC992 FOREIGN KEY (course_id) REFERENCES content (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reported_content ADD CONSTRAINT FK_594CB5529487CA85 FOREIGN KEY (content_id_id) REFERENCES content (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reported_content ADD CONSTRAINT FK_594CB552D6B1FFA1 FOREIGN KEY (reporter_id_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE validation_request ADD CONSTRAINT FK_2A12D2912DC63656 FOREIGN KEY (reviewer_id_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE validation_request ADD CONSTRAINT FK_2A12D2919487CA85 FOREIGN KEY (content_id_id) REFERENCES content (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE be_reviewer_application DROP CONSTRAINT FK_6ABCB1227A19A357');
        $this->addSql('ALTER TABLE comment DROP CONSTRAINT FK_9474526C455BE0B1');
        $this->addSql('ALTER TABLE comment DROP CONSTRAINT FK_9474526C591CC992');
        $this->addSql('ALTER TABLE content DROP CONSTRAINT FK_FEC530A9F05788E9');
        $this->addSql('ALTER TABLE content DROP CONSTRAINT FK_FEC530A99777D11E');
        $this->addSql('ALTER TABLE purchase DROP CONSTRAINT FK_6117D13B6C755722');
        $this->addSql('ALTER TABLE purchase DROP CONSTRAINT FK_6117D13B591CC992');
        $this->addSql('ALTER TABLE reported_content DROP CONSTRAINT FK_594CB5529487CA85');
        $this->addSql('ALTER TABLE reported_content DROP CONSTRAINT FK_594CB552D6B1FFA1');
        $this->addSql('ALTER TABLE validation_request DROP CONSTRAINT FK_2A12D2912DC63656');
        $this->addSql('ALTER TABLE validation_request DROP CONSTRAINT FK_2A12D2919487CA85');
        $this->addSql('DROP TABLE be_reviewer_application');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE content');
        $this->addSql('DROP TABLE purchase');
        $this->addSql('DROP TABLE reported_content');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE validation_request');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
