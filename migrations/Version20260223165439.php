<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260223165439 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE utilisateur DROP CONSTRAINT fk_1d1c63b3cf7bca0e');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3CF7BCA0E FOREIGN KEY (service_medical_id) REFERENCES service_medical (id) ON DELETE SET NULL NOT DEFERRABLE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE utilisateur DROP CONSTRAINT FK_1D1C63B3CF7BCA0E');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT fk_1d1c63b3cf7bca0e FOREIGN KEY (service_medical_id) REFERENCES service_medical (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
