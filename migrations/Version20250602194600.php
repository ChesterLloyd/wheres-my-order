<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250602194600 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add inbound email table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE inbound_email (id INT AUTO_INCREMENT NOT NULL, date_received DATETIME NOT NULL, sender VARCHAR(255) NOT NULL, recipient VARCHAR(255) NOT NULL, subject VARCHAR(255) NOT NULL, text_body LONGTEXT NOT NULL, html_body LONGTEXT NOT NULL, status VARCHAR(10) NOT NULL, added_by_id INT DEFAULT NULL, added_at DATETIME NOT NULL, updated_by_id INT DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_B3E1348855B127A4 (added_by_id), INDEX IDX_B3E13488896DBBDE (updated_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE inbound_email ADD CONSTRAINT FK_B3E1348855B127A4 FOREIGN KEY (added_by_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE inbound_email ADD CONSTRAINT FK_B3E13488896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE inbound_email DROP FOREIGN KEY FK_B3E1348855B127A4
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE inbound_email DROP FOREIGN KEY FK_B3E13488896DBBDE
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE inbound_email
        SQL);
    }
}
