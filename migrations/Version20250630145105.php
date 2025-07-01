<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250630145105 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE revamp_scan (id INT AUTO_INCREMENT NOT NULL, url LONGTEXT NOT NULL, loading_checks TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE similarity_check (id INT AUTO_INCREMENT NOT NULL, revamp_scan_id INT NOT NULL, year_from VARCHAR(4) NOT NULL, year_to VARCHAR(4) NOT NULL, is_revamp TINYINT(1) NOT NULL, similarity_rate DOUBLE PRECISION NOT NULL, INDEX IDX_74F8D0637EBCD25C (revamp_scan_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE similarity_check ADD CONSTRAINT FK_74F8D0637EBCD25C FOREIGN KEY (revamp_scan_id) REFERENCES revamp_scan (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE similarity_check DROP FOREIGN KEY FK_74F8D0637EBCD25C');
        $this->addSql('DROP TABLE revamp_scan');
        $this->addSql('DROP TABLE similarity_check');
    }
}
