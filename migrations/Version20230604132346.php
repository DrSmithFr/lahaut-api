<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230604132346 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Slot unique index to prevent duplicate slots';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX slot_unique_idx ON app_slot (monitor_uuid, fly_location_uuid, type, start_at, end_at)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX slot_unique_idx');
    }
}
