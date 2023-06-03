<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230603150136 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'create proposed slot table for planning generation';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE slot_proposed_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE app_slot_proposed (id INT NOT NULL, fly_location_uuid UUID NOT NULL, start_at TIME(0) WITHOUT TIME ZONE NOT NULL, end_at TIME(0) WITHOUT TIME ZONE NOT NULL, average_fly_duration VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9E7A857BDCE35E38 ON app_slot_proposed (fly_location_uuid)');
        $this->addSql('COMMENT ON COLUMN app_slot_proposed.fly_location_uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN app_slot_proposed.start_at IS \'(DC2Type:time_immutable)\'');
        $this->addSql('COMMENT ON COLUMN app_slot_proposed.end_at IS \'(DC2Type:time_immutable)\'');
        $this->addSql('COMMENT ON COLUMN app_slot_proposed.average_fly_duration IS \'(DC2Type:dateinterval)\'');
        $this->addSql('ALTER TABLE app_slot_proposed ADD CONSTRAINT FK_9E7A857BDCE35E38 FOREIGN KEY (fly_location_uuid) REFERENCES app_fly_location (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE slot_proposed_id_seq CASCADE');
        $this->addSql('ALTER TABLE app_slot_proposed DROP CONSTRAINT FK_9E7A857BDCE35E38');
        $this->addSql('DROP TABLE app_slot_proposed');
    }
}
