<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230720132812 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add not null constraints to message user and conversation';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE app_message ALTER user_uuid SET NOT NULL');
        $this->addSql('ALTER TABLE app_message ALTER conversation_uuid SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE app_message ALTER user_uuid DROP NOT NULL');
        $this->addSql('ALTER TABLE app_message ALTER conversation_uuid DROP NOT NULL');
    }
}
