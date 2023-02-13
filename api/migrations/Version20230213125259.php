<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230213125259 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE content DROP CONSTRAINT FK_FEC530A9F05788E9');
        $this->addSql('ALTER TABLE content ADD CONSTRAINT FK_FEC530A9F05788E9 FOREIGN KEY (creator_id_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE register_token DROP CONSTRAINT FK_893565C39D86650F');
        $this->addSql('ALTER TABLE register_token ADD CONSTRAINT FK_893565C39D86650F FOREIGN KEY (user_id_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reported_content DROP CONSTRAINT FK_594CB552D6B1FFA1');
        $this->addSql('ALTER TABLE reported_content ADD CONSTRAINT FK_594CB552D6B1FFA1 FOREIGN KEY (reporter_id_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE validation_request DROP CONSTRAINT FK_2A12D2912DC63656');
        $this->addSql('ALTER TABLE validation_request ADD CONSTRAINT FK_2A12D2912DC63656 FOREIGN KEY (reviewer_id_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE reported_content DROP CONSTRAINT fk_594cb552d6b1ffa1');
        $this->addSql('ALTER TABLE reported_content ADD CONSTRAINT fk_594cb552d6b1ffa1 FOREIGN KEY (reporter_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE validation_request DROP CONSTRAINT fk_2a12d2912dc63656');
        $this->addSql('ALTER TABLE validation_request ADD CONSTRAINT fk_2a12d2912dc63656 FOREIGN KEY (reviewer_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE content DROP CONSTRAINT fk_fec530a9f05788e9');
        $this->addSql('ALTER TABLE content ADD CONSTRAINT fk_fec530a9f05788e9 FOREIGN KEY (creator_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE register_token DROP CONSTRAINT fk_893565c39d86650f');
        $this->addSql('ALTER TABLE register_token ADD CONSTRAINT fk_893565c39d86650f FOREIGN KEY (user_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
