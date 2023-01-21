<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221229130612 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Adding User Identity, Address and Billing address';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE app_user ADD identity_anniversary DATE DEFAULT NULL');
        $this->addSql('ALTER TABLE app_user ADD identity_first_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE app_user ADD identity_last_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE app_user ADD identity_phone VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE app_user ADD identity_nationality VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE app_user ADD address_street VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE app_user ADD address_postal_code VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE app_user ADD address_city VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE app_user ADD address_country VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE app_user ADD billing_street VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE app_user ADD billing_postal_code VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE app_user ADD billing_city VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE app_user ADD billing_country VARCHAR(255) DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN app_user.identity_anniversary IS \'(DC2Type:date_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE app_user DROP identity_anniversary');
        $this->addSql('ALTER TABLE app_user DROP identity_first_name');
        $this->addSql('ALTER TABLE app_user DROP identity_last_name');
        $this->addSql('ALTER TABLE app_user DROP identity_phone');
        $this->addSql('ALTER TABLE app_user DROP identity_nationality');
        $this->addSql('ALTER TABLE app_user DROP address_street');
        $this->addSql('ALTER TABLE app_user DROP address_postal_code');
        $this->addSql('ALTER TABLE app_user DROP address_city');
        $this->addSql('ALTER TABLE app_user DROP address_country');
        $this->addSql('ALTER TABLE app_user DROP billing_street');
        $this->addSql('ALTER TABLE app_user DROP billing_postal_code');
        $this->addSql('ALTER TABLE app_user DROP billing_city');
        $this->addSql('ALTER TABLE app_user DROP billing_country');
    }
}
