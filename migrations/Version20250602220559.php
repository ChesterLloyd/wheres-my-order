<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250602220559 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add quantity to item and order_id and status to purchase';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE item ADD quantity INT NOT NULL AFTER currency
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE purchase ADD order_id LONGTEXT NOT NULL AFTER store_id, ADD status VARCHAR(12) NOT NULL AFTER currency
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE item DROP quantity
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE purchase DROP order_id, DROP status
        SQL);
    }
}
