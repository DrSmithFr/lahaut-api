<?php

declare(strict_types = 1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200517093532 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'User and medias tables';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql',
            'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE medias_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE app_medias (id INT NOT NULL, uuid UUID NOT NULL, content_type VARCHAR(255) DEFAULT NULL, size INT DEFAULT NULL, extension VARCHAR(255) NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F7D1C201D17F50A6 ON app_medias (uuid)');
        $this->addSql('COMMENT ON COLUMN app_medias.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE app_user (id INT NOT NULL, uuid UUID NOT NULL, password VARCHAR(255) NOT NULL, salt VARCHAR(255) DEFAULT NULL, roles JSON NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_by VARCHAR(255) DEFAULT NULL, updated_by VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_88BDF3E9D17F50A6 ON app_user (uuid)');
        $this->addSql('COMMENT ON COLUMN app_user.uuid IS \'(DC2Type:uuid)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql',
            'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE medias_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE user_id_seq CASCADE');
        $this->addSql('DROP TABLE app_medias');
        $this->addSql('DROP TABLE app_user');
    }
}
