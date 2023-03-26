<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230325124952 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE validation_request DROP CONSTRAINT FK_2A12D2919487CA85');
        $this->addSql('DROP INDEX uniq_2a12d2919487ca85');
        $this->addSql('DROP INDEX uniq_2a12d2912dc63656');
        $this->addSql('ALTER TABLE validation_request ADD CONSTRAINT FK_2A12D2919487CA85 FOREIGN KEY (content_id_id) REFERENCES content (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_2A12D2912DC63656 ON validation_request (reviewer_id_id)');
        $this->addSql('CREATE INDEX IDX_2A12D2919487CA85 ON validation_request (content_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE validation_request DROP CONSTRAINT fk_2a12d2919487ca85');
        $this->addSql('DROP INDEX IDX_2A12D2912DC63656');
        $this->addSql('DROP INDEX IDX_2A12D2919487CA85');
        $this->addSql('ALTER TABLE validation_request ADD CONSTRAINT fk_2a12d2919487ca85 FOREIGN KEY (content_id_id) REFERENCES content (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_2a12d2919487ca85 ON validation_request (content_id_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_2a12d2912dc63656 ON validation_request (reviewer_id_id)');
    }
}
