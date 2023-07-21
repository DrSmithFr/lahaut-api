<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230721100737 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'store roles as simple_array instead of json';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE app_user ALTER roles TYPE TEXT');
        $this->addSql('COMMENT ON COLUMN app_user.roles IS \'(DC2Type:simple_array)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE app_user ALTER roles TYPE JSON');
        $this->addSql('COMMENT ON COLUMN app_user.roles IS NULL');
    }
}
