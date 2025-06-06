<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250606183257 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add status_message column to inbound email table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE inbound_email ADD status_message VARCHAR(255) DEFAULT NULL AFTER status
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE inbound_email DROP status_message
        SQL);
    }
}
