<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250805200030 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        // Zuerst die Spalte als nullable hinzufügen
        $this->addSql('ALTER TABLE "user" ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN "user".created_at IS \'(DC2Type:datetime_immutable)\'');
        
        // Bestehende Benutzer mit aktuellem Zeitstempel füllen
        $this->addSql('UPDATE "user" SET created_at = NOW() WHERE created_at IS NULL');
        
        // Dann die Spalte auf NOT NULL setzen
        $this->addSql('ALTER TABLE "user" ALTER COLUMN created_at SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "user" DROP created_at');
    }
}
