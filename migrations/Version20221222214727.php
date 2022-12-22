<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221222214727 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Chat related entities';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE message_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE participant_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE app_conversation (uuid UUID NOT NULL, last_message_id INT DEFAULT NULL, PRIMARY KEY(uuid))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_48003C68BA0E79C3 ON app_conversation (last_message_id)');
        $this->addSql('COMMENT ON COLUMN app_conversation.uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE app_message (id INT NOT NULL, user_uuid UUID DEFAULT NULL, conversation_uuid UUID DEFAULT NULL, content TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_5BE0B032ABFE1C6F ON app_message (user_uuid)');
        $this->addSql('CREATE INDEX IDX_5BE0B03260033BA ON app_message (conversation_uuid)');
        $this->addSql('COMMENT ON COLUMN app_message.user_uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN app_message.conversation_uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE app_participant (id INT NOT NULL, user_uuid UUID DEFAULT NULL, conversation_uuid UUID DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8E7A4DADABFE1C6F ON app_participant (user_uuid)');
        $this->addSql('CREATE INDEX IDX_8E7A4DAD60033BA ON app_participant (conversation_uuid)');
        $this->addSql('COMMENT ON COLUMN app_participant.user_uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN app_participant.conversation_uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE app_conversation ADD CONSTRAINT FK_48003C68BA0E79C3 FOREIGN KEY (last_message_id) REFERENCES app_message (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_message ADD CONSTRAINT FK_5BE0B032ABFE1C6F FOREIGN KEY (user_uuid) REFERENCES app_user (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_message ADD CONSTRAINT FK_5BE0B03260033BA FOREIGN KEY (conversation_uuid) REFERENCES app_conversation (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_participant ADD CONSTRAINT FK_8E7A4DADABFE1C6F FOREIGN KEY (user_uuid) REFERENCES app_user (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE app_participant ADD CONSTRAINT FK_8E7A4DAD60033BA FOREIGN KEY (conversation_uuid) REFERENCES app_conversation (uuid) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE message_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE participant_id_seq CASCADE');
        $this->addSql('ALTER TABLE app_conversation DROP CONSTRAINT FK_48003C68BA0E79C3');
        $this->addSql('ALTER TABLE app_message DROP CONSTRAINT FK_5BE0B032ABFE1C6F');
        $this->addSql('ALTER TABLE app_message DROP CONSTRAINT FK_5BE0B03260033BA');
        $this->addSql('ALTER TABLE app_participant DROP CONSTRAINT FK_8E7A4DADABFE1C6F');
        $this->addSql('ALTER TABLE app_participant DROP CONSTRAINT FK_8E7A4DAD60033BA');
        $this->addSql('DROP TABLE app_conversation');
        $this->addSql('DROP TABLE app_message');
        $this->addSql('DROP TABLE app_participant');
    }
}
