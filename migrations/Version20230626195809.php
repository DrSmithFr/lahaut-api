<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230626195809 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Renaming Fly to Activity';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE app_slot DROP CONSTRAINT fk_a92005c7444dce2');
        $this->addSql('ALTER TABLE app_slot_proposed DROP CONSTRAINT fk_9e7a857b444dce2');
        $this->addSql('CREATE TABLE app_activity_location (uuid UUID NOT NULL, take_off UUID NOT NULL, meeting UUID NOT NULL, landing UUID NOT NULL, identifier VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(uuid))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5FD1945A772E836A ON app_activity_location (identifier)');
        $this->addSql('CREATE INDEX IDX_5FD1945A148B9B53 ON app_activity_location (take_off)');
        $this->addSql('CREATE INDEX IDX_5FD1945AF515E139 ON app_activity_location (meeting)');
        $this->addSql('CREATE INDEX IDX_5FD1945AEF3ACE15 ON app_activity_location (landing)');
        $this->addSql('COMMENT ON COLUMN app_activity_location.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN app_activity_location.take_off IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN app_activity_location.meeting IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN app_activity_location.landing IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE app_activity_type (uuid UUID NOT NULL, activity_location UUID NOT NULL, identifier VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(uuid))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1567B117772E836A ON app_activity_type (identifier)');
        $this->addSql('CREATE INDEX IDX_1567B117FE9439C5 ON app_activity_type (activity_location)');
        $this->addSql('COMMENT ON COLUMN app_activity_type.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN app_activity_type.activity_location IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE app_activity_location ADD CONSTRAINT FK_5FD1945A148B9B53 FOREIGN KEY (take_off) REFERENCES app_place_point (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_activity_location ADD CONSTRAINT FK_5FD1945AF515E139 FOREIGN KEY (meeting) REFERENCES app_place_point (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_activity_location ADD CONSTRAINT FK_5FD1945AEF3ACE15 FOREIGN KEY (landing) REFERENCES app_place_point (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_activity_type ADD CONSTRAINT FK_1567B117FE9439C5 FOREIGN KEY (activity_location) REFERENCES app_activity_location (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_fly_type DROP CONSTRAINT fk_cc4bc4f7a7f7d2f');
        $this->addSql('ALTER TABLE app_fly_location DROP CONSTRAINT fk_b8f167ae148b9b53');
        $this->addSql('ALTER TABLE app_fly_location DROP CONSTRAINT fk_b8f167aef515e139');
        $this->addSql('ALTER TABLE app_fly_location DROP CONSTRAINT fk_b8f167aeef3ace15');
        $this->addSql('DROP TABLE app_fly_type');
        $this->addSql('DROP TABLE app_fly_location');
        $this->addSql('DROP INDEX idx_a92005c7444dce2');
        $this->addSql('DROP INDEX slot_unique_idx');
        $this->addSql('ALTER TABLE app_slot RENAME COLUMN fly_type TO activity_type');
        $this->addSql('ALTER TABLE app_slot RENAME COLUMN average_fly_duration TO average_activity_duration');
        $this->addSql('ALTER TABLE app_slot ADD CONSTRAINT FK_A92005C78F1A8CBB FOREIGN KEY (activity_type) REFERENCES app_activity_type (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_A92005C78F1A8CBB ON app_slot (activity_type)');
        $this->addSql('CREATE UNIQUE INDEX slot_unique_idx ON app_slot (monitor_uuid, activity_type, start_at, end_at)');
        $this->addSql('DROP INDEX idx_9e7a857b444dce2');
        $this->addSql('ALTER TABLE app_slot_proposed RENAME COLUMN fly_type TO activity_type');
        $this->addSql('ALTER TABLE app_slot_proposed RENAME COLUMN average_fly_duration TO average_activity_duration');
        $this->addSql('ALTER TABLE app_slot_proposed ADD CONSTRAINT FK_9E7A857B8F1A8CBB FOREIGN KEY (activity_type) REFERENCES app_activity_type (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_9E7A857B8F1A8CBB ON app_slot_proposed (activity_type)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE app_slot DROP CONSTRAINT FK_A92005C78F1A8CBB');
        $this->addSql('ALTER TABLE app_slot_proposed DROP CONSTRAINT FK_9E7A857B8F1A8CBB');
        $this->addSql('CREATE TABLE app_fly_type (uuid UUID NOT NULL, fly_location UUID NOT NULL, identifier VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(uuid))');
        $this->addSql('CREATE INDEX idx_cc4bc4f7a7f7d2f ON app_fly_type (fly_location)');
        $this->addSql('CREATE UNIQUE INDEX uniq_cc4bc4f772e836a ON app_fly_type (identifier)');
        $this->addSql('COMMENT ON COLUMN app_fly_type.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN app_fly_type.fly_location IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE app_fly_location (uuid UUID NOT NULL, take_off UUID NOT NULL, meeting UUID NOT NULL, landing UUID NOT NULL, identifier VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(uuid))');
        $this->addSql('CREATE INDEX idx_b8f167aeef3ace15 ON app_fly_location (landing)');
        $this->addSql('CREATE INDEX idx_b8f167aef515e139 ON app_fly_location (meeting)');
        $this->addSql('CREATE INDEX idx_b8f167ae148b9b53 ON app_fly_location (take_off)');
        $this->addSql('CREATE UNIQUE INDEX uniq_b8f167ae772e836a ON app_fly_location (identifier)');
        $this->addSql('COMMENT ON COLUMN app_fly_location.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN app_fly_location.take_off IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN app_fly_location.meeting IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN app_fly_location.landing IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE app_fly_type ADD CONSTRAINT fk_cc4bc4f7a7f7d2f FOREIGN KEY (fly_location) REFERENCES app_fly_location (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_fly_location ADD CONSTRAINT fk_b8f167ae148b9b53 FOREIGN KEY (take_off) REFERENCES app_place_point (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_fly_location ADD CONSTRAINT fk_b8f167aef515e139 FOREIGN KEY (meeting) REFERENCES app_place_point (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_fly_location ADD CONSTRAINT fk_b8f167aeef3ace15 FOREIGN KEY (landing) REFERENCES app_place_point (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_activity_location DROP CONSTRAINT FK_5FD1945A148B9B53');
        $this->addSql('ALTER TABLE app_activity_location DROP CONSTRAINT FK_5FD1945AF515E139');
        $this->addSql('ALTER TABLE app_activity_location DROP CONSTRAINT FK_5FD1945AEF3ACE15');
        $this->addSql('ALTER TABLE app_activity_type DROP CONSTRAINT FK_1567B117FE9439C5');
        $this->addSql('DROP TABLE app_activity_location');
        $this->addSql('DROP TABLE app_activity_type');
        $this->addSql('DROP INDEX IDX_A92005C78F1A8CBB');
        $this->addSql('DROP INDEX slot_unique_idx');
        $this->addSql('ALTER TABLE app_slot RENAME COLUMN activity_type TO fly_type');
        $this->addSql('ALTER TABLE app_slot RENAME COLUMN average_activity_duration TO average_fly_duration');
        $this->addSql('ALTER TABLE app_slot ADD CONSTRAINT fk_a92005c7444dce2 FOREIGN KEY (fly_type) REFERENCES app_fly_type (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_a92005c7444dce2 ON app_slot (fly_type)');
        $this->addSql('CREATE UNIQUE INDEX slot_unique_idx ON app_slot (monitor_uuid, fly_type, start_at, end_at)');
        $this->addSql('DROP INDEX IDX_9E7A857B8F1A8CBB');
        $this->addSql('ALTER TABLE app_slot_proposed RENAME COLUMN activity_type TO fly_type');
        $this->addSql('ALTER TABLE app_slot_proposed RENAME COLUMN average_activity_duration TO average_fly_duration');
        $this->addSql('ALTER TABLE app_slot_proposed ADD CONSTRAINT fk_9e7a857b444dce2 FOREIGN KEY (fly_type) REFERENCES app_fly_type (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_9e7a857b444dce2 ON app_slot_proposed (fly_type)');
    }
}
