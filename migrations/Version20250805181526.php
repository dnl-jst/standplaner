<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250805181526 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create campaign management entities: CampaignStand, Participant, and StandParticipation tables';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE campaign_stand (id SERIAL NOT NULL, start_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, end_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, district VARCHAR(255) NOT NULL, address VARCHAR(500) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN campaign_stand.start_time IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN campaign_stand.end_time IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN campaign_stand.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE participant (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN participant.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE stand_participation (id SERIAL NOT NULL, campaign_stand_id INT NOT NULL, participant_id INT NOT NULL, status VARCHAR(50) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_332CB1F9A54402F8 ON stand_participation (campaign_stand_id)');
        $this->addSql('CREATE INDEX IDX_332CB1F99D1C3019 ON stand_participation (participant_id)');
        $this->addSql('COMMENT ON COLUMN stand_participation.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN stand_participation.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE stand_participation ADD CONSTRAINT FK_332CB1F9A54402F8 FOREIGN KEY (campaign_stand_id) REFERENCES campaign_stand (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE stand_participation ADD CONSTRAINT FK_332CB1F99D1C3019 FOREIGN KEY (participant_id) REFERENCES participant (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE stand_participation DROP CONSTRAINT FK_332CB1F9A54402F8');
        $this->addSql('ALTER TABLE stand_participation DROP CONSTRAINT FK_332CB1F99D1C3019');
        $this->addSql('DROP TABLE campaign_stand');
        $this->addSql('DROP TABLE participant');
        $this->addSql('DROP TABLE stand_participation');
    }
}
