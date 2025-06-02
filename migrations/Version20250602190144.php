<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250602190144 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add inbound_email column to user table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE user ADD inbound_email VARCHAR(255) NOT NULL AFTER email
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE user DROP inbound_email
        SQL);
    }
}
