<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230325155131 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE be_reviewer_application_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE be_reviewer_application (id INT NOT NULL, contributor_id INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, status VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6ABCB1227A19A357 ON be_reviewer_application (contributor_id)');
        $this->addSql('COMMENT ON COLUMN be_reviewer_application.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE be_reviewer_application ADD CONSTRAINT FK_6ABCB1227A19A357 FOREIGN KEY (contributor_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE be_reviewer_application_id_seq CASCADE');
        $this->addSql('ALTER TABLE be_reviewer_application DROP CONSTRAINT FK_6ABCB1227A19A357');
        $this->addSql('DROP TABLE be_reviewer_application');
    }
}
