<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230429123754 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX idx_6abcb1227a19a357');
        $this->addSql('ALTER TABLE be_reviewer_application ALTER contributor_id DROP NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6ABCB1227A19A357 ON be_reviewer_application (contributor_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX UNIQ_6ABCB1227A19A357');
        $this->addSql('ALTER TABLE be_reviewer_application ALTER contributor_id SET NOT NULL');
        $this->addSql('CREATE INDEX idx_6abcb1227a19a357 ON be_reviewer_application (contributor_id)');
    }
}