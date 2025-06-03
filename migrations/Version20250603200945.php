<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250603200945 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add purchase_id to inbound_email';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE inbound_email ADD purchase_id INT DEFAULT NULL AFTER id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE inbound_email ADD CONSTRAINT FK_B3E13488558FBEB9 FOREIGN KEY (purchase_id) REFERENCES purchase (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_B3E13488558FBEB9 ON inbound_email (purchase_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE inbound_email DROP FOREIGN KEY FK_B3E13488558FBEB9
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_B3E13488558FBEB9 ON inbound_email
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE inbound_email DROP purchase_id
        SQL);
    }
}
