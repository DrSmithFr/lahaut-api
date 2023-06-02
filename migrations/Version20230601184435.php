<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230601184435 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add sent_at column to app_message table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE app_message ADD sent_at TIMESTAMP(0) WITH TIME ZONE NOT NULL');
        $this->addSql('COMMENT ON COLUMN app_message.sent_at IS \'(DC2Type:datetimetz_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE app_message DROP sent_at');
    }
}
