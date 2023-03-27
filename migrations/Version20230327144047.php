<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230327144047 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'FlyLocation';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE app_fly_location (uuid UUID NOT NULL, take_off_uuid UUID NOT NULL, meeting_uuid UUID NOT NULL, landing_uuid UUID NOT NULL, identifier VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(uuid))');
        $this->addSql('CREATE INDEX IDX_B8F167AE5A66D037 ON app_fly_location (take_off_uuid)');
        $this->addSql('CREATE INDEX IDX_B8F167AE3D8EA874 ON app_fly_location (meeting_uuid)');
        $this->addSql('CREATE INDEX IDX_B8F167AE42D22E1F ON app_fly_location (landing_uuid)');
        $this->addSql('COMMENT ON COLUMN app_fly_location.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN app_fly_location.take_off_uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN app_fly_location.meeting_uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN app_fly_location.landing_uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE app_fly_location ADD CONSTRAINT FK_B8F167AE5A66D037 FOREIGN KEY (take_off_uuid) REFERENCES app_place_point (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_fly_location ADD CONSTRAINT FK_B8F167AE3D8EA874 FOREIGN KEY (meeting_uuid) REFERENCES app_place_point (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_fly_location ADD CONSTRAINT FK_B8F167AE42D22E1F FOREIGN KEY (landing_uuid) REFERENCES app_place_point (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_slot DROP CONSTRAINT fk_a92005c7e16f497a');
        $this->addSql('ALTER TABLE app_slot DROP CONSTRAINT fk_a92005c75a66d037');
        $this->addSql('ALTER TABLE app_slot DROP CONSTRAINT fk_a92005c742d22e1f');
        $this->addSql('DROP INDEX idx_a92005c742d22e1f');
        $this->addSql('DROP INDEX idx_a92005c75a66d037');
        $this->addSql('DROP INDEX idx_a92005c7e16f497a');
        $this->addSql('ALTER TABLE app_slot ADD fly_location_uuid UUID NOT NULL');
        $this->addSql('ALTER TABLE app_slot DROP meeting_point_uuid');
        $this->addSql('ALTER TABLE app_slot DROP take_off_uuid');
        $this->addSql('ALTER TABLE app_slot DROP landing_uuid');
        $this->addSql('COMMENT ON COLUMN app_slot.fly_location_uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE app_slot ADD CONSTRAINT FK_A92005C7DCE35E38 FOREIGN KEY (fly_location_uuid) REFERENCES app_fly_location (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_A92005C7DCE35E38 ON app_slot (fly_location_uuid)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE app_slot DROP CONSTRAINT FK_A92005C7DCE35E38');
        $this->addSql('ALTER TABLE app_fly_location DROP CONSTRAINT FK_B8F167AE5A66D037');
        $this->addSql('ALTER TABLE app_fly_location DROP CONSTRAINT FK_B8F167AE3D8EA874');
        $this->addSql('ALTER TABLE app_fly_location DROP CONSTRAINT FK_B8F167AE42D22E1F');
        $this->addSql('DROP TABLE app_fly_location');
        $this->addSql('DROP INDEX IDX_A92005C7DCE35E38');
        $this->addSql('ALTER TABLE app_slot ADD take_off_uuid UUID NOT NULL');
        $this->addSql('ALTER TABLE app_slot ADD landing_uuid UUID NOT NULL');
        $this->addSql('ALTER TABLE app_slot RENAME COLUMN fly_location_uuid TO meeting_point_uuid');
        $this->addSql('COMMENT ON COLUMN app_slot.take_off_uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN app_slot.landing_uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE app_slot ADD CONSTRAINT fk_a92005c7e16f497a FOREIGN KEY (meeting_point_uuid) REFERENCES app_place_point (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_slot ADD CONSTRAINT fk_a92005c75a66d037 FOREIGN KEY (take_off_uuid) REFERENCES app_place_point (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_slot ADD CONSTRAINT fk_a92005c742d22e1f FOREIGN KEY (landing_uuid) REFERENCES app_place_point (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_a92005c742d22e1f ON app_slot (landing_uuid)');
        $this->addSql('CREATE INDEX idx_a92005c75a66d037 ON app_slot (take_off_uuid)');
        $this->addSql('CREATE INDEX idx_a92005c7e16f497a ON app_slot (meeting_point_uuid)');
    }
}
