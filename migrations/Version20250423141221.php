<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250423141221 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE address (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE appointment (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, address_id INTEGER DEFAULT NULL, start DATETIME NOT NULL, "end" DATETIME NOT NULL, CONSTRAINT FK_FE38F844F5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_FE38F844F5B7AF75 ON appointment (address_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE availability_slot (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, address_id INTEGER DEFAULT NULL, weekdays CLOB DEFAULT NULL --(DC2Type:json)
            , date DATE DEFAULT NULL, start_time TIME NOT NULL, end_time TIME NOT NULL, CONSTRAINT FK_1C11DC9EF5B7AF75 FOREIGN KEY (address_id) REFERENCES address (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_1C11DC9EF5B7AF75 ON availability_slot (address_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE reason (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, duration_minutes INTEGER NOT NULL)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE slot_reason (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, availability_slot_id INTEGER DEFAULT NULL, reason_id INTEGER DEFAULT NULL, CONSTRAINT FK_33BB000ED6F1FA37 FOREIGN KEY (availability_slot_id) REFERENCES availability_slot (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_33BB000E59BB1592 FOREIGN KEY (reason_id) REFERENCES reason (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_33BB000ED6F1FA37 ON slot_reason (availability_slot_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_33BB000E59BB1592 ON slot_reason (reason_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE unavailability (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, start DATETIME NOT NULL, "end" DATETIME NOT NULL)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE address
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE appointment
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE availability_slot
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE reason
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE slot_reason
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE unavailability
        SQL);
    }
}
