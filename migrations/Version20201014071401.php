<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20201014071401
 */
final class Version20201014071401 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->addSql('CREATE SEQUENCE communications_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE contacts_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE communications (id INT NOT NULL, contact_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_2C2F5B4EE7A1254A ON communications (contact_id)');
        $this->addSql('CREATE TABLE contacts (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE user_contact (user_id INT NOT NULL, contact_id INT NOT NULL, PRIMARY KEY(user_id, contact_id))');
        $this->addSql('CREATE INDEX IDX_146FF832A76ED395 ON user_contact (user_id)');
        $this->addSql('CREATE INDEX IDX_146FF832E7A1254A ON user_contact (contact_id)');
        $this->addSql('ALTER TABLE communications ADD CONSTRAINT FK_2C2F5B4EE7A1254A FOREIGN KEY (contact_id) REFERENCES contacts (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_contact ADD CONSTRAINT FK_146FF832A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_contact ADD CONSTRAINT FK_146FF832E7A1254A FOREIGN KEY (contact_id) REFERENCES contacts (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE communications DROP CONSTRAINT FK_2C2F5B4EE7A1254A');
        $this->addSql('ALTER TABLE user_contact DROP CONSTRAINT FK_146FF832E7A1254A');
        $this->addSql('DROP SEQUENCE communications_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE contacts_id_seq CASCADE');
        $this->addSql('DROP TABLE communications');
        $this->addSql('DROP TABLE contacts');
        $this->addSql('DROP TABLE user_contact');
    }
}
