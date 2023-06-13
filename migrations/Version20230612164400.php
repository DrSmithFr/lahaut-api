<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230612164400 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'FlyType entity';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE app_fly_type (uuid UUID NOT NULL, fly_location UUID NOT NULL, identifier VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(uuid))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CC4BC4F772E836A ON app_fly_type (identifier)');
        $this->addSql('CREATE INDEX IDX_CC4BC4F7A7F7D2F ON app_fly_type (fly_location)');
        $this->addSql('COMMENT ON COLUMN app_fly_type.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN app_fly_type.fly_location IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE app_fly_type ADD CONSTRAINT FK_CC4BC4F7A7F7D2F FOREIGN KEY (fly_location) REFERENCES app_fly_location (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_fly_location DROP CONSTRAINT fk_b8f167ae5a66d037');
        $this->addSql('ALTER TABLE app_fly_location DROP CONSTRAINT fk_b8f167ae3d8ea874');
        $this->addSql('ALTER TABLE app_fly_location DROP CONSTRAINT fk_b8f167ae42d22e1f');
        $this->addSql('DROP INDEX idx_b8f167ae42d22e1f');
        $this->addSql('DROP INDEX idx_b8f167ae3d8ea874');
        $this->addSql('DROP INDEX idx_b8f167ae5a66d037');
        $this->addSql('ALTER TABLE app_fly_location ADD take_off UUID NOT NULL');
        $this->addSql('ALTER TABLE app_fly_location ADD meeting UUID NOT NULL');
        $this->addSql('ALTER TABLE app_fly_location ADD landing UUID NOT NULL');
        $this->addSql('ALTER TABLE app_fly_location DROP take_off_uuid');
        $this->addSql('ALTER TABLE app_fly_location DROP meeting_uuid');
        $this->addSql('ALTER TABLE app_fly_location DROP landing_uuid');
        $this->addSql('COMMENT ON COLUMN app_fly_location.take_off IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN app_fly_location.meeting IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN app_fly_location.landing IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE app_fly_location ADD CONSTRAINT FK_B8F167AE148B9B53 FOREIGN KEY (take_off) REFERENCES app_place_point (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_fly_location ADD CONSTRAINT FK_B8F167AEF515E139 FOREIGN KEY (meeting) REFERENCES app_place_point (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_fly_location ADD CONSTRAINT FK_B8F167AEEF3ACE15 FOREIGN KEY (landing) REFERENCES app_place_point (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B8F167AE772E836A ON app_fly_location (identifier)');
        $this->addSql('CREATE INDEX IDX_B8F167AE148B9B53 ON app_fly_location (take_off)');
        $this->addSql('CREATE INDEX IDX_B8F167AEF515E139 ON app_fly_location (meeting)');
        $this->addSql('CREATE INDEX IDX_B8F167AEEF3ACE15 ON app_fly_location (landing)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4736CEBB772E836A ON app_place_point (identifier)');
        $this->addSql('ALTER TABLE app_slot DROP CONSTRAINT fk_a92005c7dce35e38');
        $this->addSql('DROP INDEX idx_a92005c7dce35e38');
        $this->addSql('DROP INDEX slot_unique_idx');
        $this->addSql('ALTER TABLE app_slot DROP type');
        $this->addSql('ALTER TABLE app_slot RENAME COLUMN fly_location_uuid TO fly_type');
        $this->addSql('ALTER TABLE app_slot ADD CONSTRAINT FK_A92005C7444DCE2 FOREIGN KEY (fly_type) REFERENCES app_fly_type (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_A92005C7444DCE2 ON app_slot (fly_type)');
        $this->addSql('CREATE UNIQUE INDEX slot_unique_idx ON app_slot (monitor_uuid, fly_type, start_at, end_at)');
        $this->addSql('ALTER TABLE app_slot_proposed DROP CONSTRAINT fk_9e7a857bdce35e38');
        $this->addSql('DROP INDEX idx_9e7a857bdce35e38');
        $this->addSql('ALTER TABLE app_slot_proposed DROP type');
        $this->addSql('ALTER TABLE app_slot_proposed RENAME COLUMN fly_location_uuid TO fly_type');
        $this->addSql('ALTER TABLE app_slot_proposed ADD CONSTRAINT FK_9E7A857B444DCE2 FOREIGN KEY (fly_type) REFERENCES app_fly_type (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_9E7A857B444DCE2 ON app_slot_proposed (fly_type)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE app_slot DROP CONSTRAINT FK_A92005C7444DCE2');
        $this->addSql('ALTER TABLE app_slot_proposed DROP CONSTRAINT FK_9E7A857B444DCE2');
        $this->addSql('ALTER TABLE app_fly_type DROP CONSTRAINT FK_CC4BC4F7A7F7D2F');
        $this->addSql('DROP TABLE app_fly_type');
        $this->addSql('DROP INDEX UNIQ_4736CEBB772E836A');
        $this->addSql('DROP INDEX IDX_9E7A857B444DCE2');
        $this->addSql('ALTER TABLE app_slot_proposed ADD type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE app_slot_proposed RENAME COLUMN fly_type TO fly_location_uuid');
        $this->addSql('ALTER TABLE app_slot_proposed ADD CONSTRAINT fk_9e7a857bdce35e38 FOREIGN KEY (fly_location_uuid) REFERENCES app_fly_location (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_9e7a857bdce35e38 ON app_slot_proposed (fly_location_uuid)');
        $this->addSql('ALTER TABLE app_fly_location DROP CONSTRAINT FK_B8F167AE148B9B53');
        $this->addSql('ALTER TABLE app_fly_location DROP CONSTRAINT FK_B8F167AEF515E139');
        $this->addSql('ALTER TABLE app_fly_location DROP CONSTRAINT FK_B8F167AEEF3ACE15');
        $this->addSql('DROP INDEX UNIQ_B8F167AE772E836A');
        $this->addSql('DROP INDEX IDX_B8F167AE148B9B53');
        $this->addSql('DROP INDEX IDX_B8F167AEF515E139');
        $this->addSql('DROP INDEX IDX_B8F167AEEF3ACE15');
        $this->addSql('ALTER TABLE app_fly_location ADD take_off_uuid UUID NOT NULL');
        $this->addSql('ALTER TABLE app_fly_location ADD meeting_uuid UUID NOT NULL');
        $this->addSql('ALTER TABLE app_fly_location ADD landing_uuid UUID NOT NULL');
        $this->addSql('ALTER TABLE app_fly_location DROP take_off');
        $this->addSql('ALTER TABLE app_fly_location DROP meeting');
        $this->addSql('ALTER TABLE app_fly_location DROP landing');
        $this->addSql('COMMENT ON COLUMN app_fly_location.take_off_uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN app_fly_location.meeting_uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN app_fly_location.landing_uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE app_fly_location ADD CONSTRAINT fk_b8f167ae5a66d037 FOREIGN KEY (take_off_uuid) REFERENCES app_place_point (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_fly_location ADD CONSTRAINT fk_b8f167ae3d8ea874 FOREIGN KEY (meeting_uuid) REFERENCES app_place_point (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_fly_location ADD CONSTRAINT fk_b8f167ae42d22e1f FOREIGN KEY (landing_uuid) REFERENCES app_place_point (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_b8f167ae42d22e1f ON app_fly_location (landing_uuid)');
        $this->addSql('CREATE INDEX idx_b8f167ae3d8ea874 ON app_fly_location (meeting_uuid)');
        $this->addSql('CREATE INDEX idx_b8f167ae5a66d037 ON app_fly_location (take_off_uuid)');
        $this->addSql('DROP INDEX IDX_A92005C7444DCE2');
        $this->addSql('DROP INDEX slot_unique_idx');
        $this->addSql('ALTER TABLE app_slot ADD type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE app_slot RENAME COLUMN fly_type TO fly_location_uuid');
        $this->addSql('ALTER TABLE app_slot ADD CONSTRAINT fk_a92005c7dce35e38 FOREIGN KEY (fly_location_uuid) REFERENCES app_fly_location (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_a92005c7dce35e38 ON app_slot (fly_location_uuid)');
        $this->addSql('CREATE UNIQUE INDEX slot_unique_idx ON app_slot (monitor_uuid, fly_location_uuid, type, start_at, end_at)');
    }
}
