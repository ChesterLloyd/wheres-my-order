<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250601153432 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add purchase tables';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE item (id INT AUTO_INCREMENT NOT NULL, purchase_id INT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, amount DOUBLE PRECISION NOT NULL, currency VARCHAR(3) NOT NULL, added_by_id INT DEFAULT NULL, added_at DATETIME NOT NULL, updated_by_id INT DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_1F1B251E55B127A4 (added_by_id), INDEX IDX_1F1B251E896DBBDE (updated_by_id), INDEX IDX_1F1B251E558FBEB9 (purchase_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE purchase (id INT AUTO_INCREMENT NOT NULL, store_id INT NOT NULL, order_date DATETIME NOT NULL, amount DOUBLE PRECISION NOT NULL, currency VARCHAR(3) NOT NULL, tracking_courier VARCHAR(255) DEFAULT NULL, tracking_url LONGTEXT DEFAULT NULL, added_by_id INT DEFAULT NULL, added_at DATETIME NOT NULL, updated_by_id INT DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_6117D13B55B127A4 (added_by_id), INDEX IDX_6117D13B896DBBDE (updated_by_id), INDEX IDX_6117D13BB092A811 (store_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE store (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, website VARCHAR(255) DEFAULT NULL, added_by_id INT DEFAULT NULL, added_at DATETIME NOT NULL, updated_by_id INT DEFAULT NULL, updated_at DATETIME DEFAULT NULL, INDEX IDX_FF57587755B127A4 (added_by_id), INDEX IDX_FF575877896DBBDE (updated_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE item ADD CONSTRAINT FK_1F1B251E558FBEB9 FOREIGN KEY (purchase_id) REFERENCES purchase (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE item ADD CONSTRAINT FK_1F1B251E55B127A4 FOREIGN KEY (added_by_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE item ADD CONSTRAINT FK_1F1B251E896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE purchase ADD CONSTRAINT FK_6117D13BB092A811 FOREIGN KEY (store_id) REFERENCES store (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE purchase ADD CONSTRAINT FK_6117D13B55B127A4 FOREIGN KEY (added_by_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE purchase ADD CONSTRAINT FK_6117D13B896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE store ADD CONSTRAINT FK_FF57587755B127A4 FOREIGN KEY (added_by_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE store ADD CONSTRAINT FK_FF575877896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE item DROP FOREIGN KEY FK_1F1B251E55B127A4
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE item DROP FOREIGN KEY FK_1F1B251E896DBBDE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE item DROP FOREIGN KEY FK_1F1B251E558FBEB9
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE purchase DROP FOREIGN KEY FK_6117D13B55B127A4
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE purchase DROP FOREIGN KEY FK_6117D13B896DBBDE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE purchase DROP FOREIGN KEY FK_6117D13BB092A811
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE store DROP FOREIGN KEY FK_FF57587755B127A4
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE store DROP FOREIGN KEY FK_FF575877896DBBDE
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE item
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE purchase
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE store
        SQL);
    }
}
