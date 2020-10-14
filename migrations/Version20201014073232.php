<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20201014073232
 */
final class Version20201014073232 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE communications ADD value VARCHAR(255) NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema) : void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE communications DROP value');
    }
}
