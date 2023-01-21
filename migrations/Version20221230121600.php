<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221230121600 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Fly related entities';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE slot_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE app_place (uuid UUID NOT NULL, identifier VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, latitude VARCHAR(255) NOT NULL, longitude VARCHAR(255) NOT NULL, description TEXT NOT NULL, address_street VARCHAR(255) DEFAULT NULL, address_postal_code VARCHAR(255) DEFAULT NULL, address_city VARCHAR(255) DEFAULT NULL, address_country VARCHAR(255) DEFAULT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(uuid))');
        $this->addSql('COMMENT ON COLUMN app_place.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE app_slot (id INT NOT NULL, monitor_uuid UUID DEFAULT NULL, meeting_point_uuid UUID DEFAULT NULL, take_off_uuid UUID DEFAULT NULL, landing_uuid UUID DEFAULT NULL, start_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, end_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, average_fly_duration VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A92005C7689C1247 ON app_slot (monitor_uuid)');
        $this->addSql('CREATE INDEX IDX_A92005C7E16F497A ON app_slot (meeting_point_uuid)');
        $this->addSql('CREATE INDEX IDX_A92005C75A66D037 ON app_slot (take_off_uuid)');
        $this->addSql('CREATE INDEX IDX_A92005C742D22E1F ON app_slot (landing_uuid)');
        $this->addSql('COMMENT ON COLUMN app_slot.monitor_uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN app_slot.meeting_point_uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN app_slot.take_off_uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN app_slot.landing_uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN app_slot.start_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN app_slot.end_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN app_slot.average_fly_duration IS \'(DC2Type:dateinterval)\'');
        $this->addSql('ALTER TABLE app_slot ADD CONSTRAINT FK_A92005C7689C1247 FOREIGN KEY (monitor_uuid) REFERENCES app_user (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_slot ADD CONSTRAINT FK_A92005C7E16F497A FOREIGN KEY (meeting_point_uuid) REFERENCES app_place (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_slot ADD CONSTRAINT FK_A92005C75A66D037 FOREIGN KEY (take_off_uuid) REFERENCES app_place (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_slot ADD CONSTRAINT FK_A92005C742D22E1F FOREIGN KEY (landing_uuid) REFERENCES app_place (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE slot_id_seq CASCADE');
        $this->addSql('ALTER TABLE app_slot DROP CONSTRAINT FK_A92005C7689C1247');
        $this->addSql('ALTER TABLE app_slot DROP CONSTRAINT FK_A92005C7E16F497A');
        $this->addSql('ALTER TABLE app_slot DROP CONSTRAINT FK_A92005C75A66D037');
        $this->addSql('ALTER TABLE app_slot DROP CONSTRAINT FK_A92005C742D22E1F');
        $this->addSql('DROP TABLE app_place');
        $this->addSql('DROP TABLE app_slot');
    }
}
