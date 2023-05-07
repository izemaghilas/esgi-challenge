<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230507152639 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE purchase_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE purchase (id INT NOT NULL, buyer_id INT NOT NULL, course_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6117D13B6C755722 ON purchase (buyer_id)');
        $this->addSql('CREATE INDEX IDX_6117D13B591CC992 ON purchase (course_id)');
        $this->addSql('ALTER TABLE purchase ADD CONSTRAINT FK_6117D13B6C755722 FOREIGN KEY (buyer_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE purchase ADD CONSTRAINT FK_6117D13B591CC992 FOREIGN KEY (course_id) REFERENCES content (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_content DROP CONSTRAINT fk_a6c82ea3a76ed395');
        $this->addSql('ALTER TABLE user_content DROP CONSTRAINT fk_a6c82ea384a0a3ed');
        $this->addSql('DROP TABLE user_content');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE purchase_id_seq CASCADE');
        $this->addSql('CREATE TABLE user_content (user_id INT NOT NULL, content_id INT NOT NULL, PRIMARY KEY(user_id, content_id))');
        $this->addSql('CREATE INDEX idx_a6c82ea384a0a3ed ON user_content (content_id)');
        $this->addSql('CREATE INDEX idx_a6c82ea3a76ed395 ON user_content (user_id)');
        $this->addSql('ALTER TABLE user_content ADD CONSTRAINT fk_a6c82ea3a76ed395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_content ADD CONSTRAINT fk_a6c82ea384a0a3ed FOREIGN KEY (content_id) REFERENCES content (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE purchase DROP CONSTRAINT FK_6117D13B6C755722');
        $this->addSql('ALTER TABLE purchase DROP CONSTRAINT FK_6117D13B591CC992');
        $this->addSql('DROP TABLE purchase');
    }
}
