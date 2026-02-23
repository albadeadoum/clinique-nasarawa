<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260223163905 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE patient ALTER sexe TYPE VARCHAR(10)');
        $this->addSql('ALTER TABLE patient ALTER groupe_sanguin TYPE VARCHAR(10)');
        $this->addSql('ALTER TABLE utilisateur ALTER statut TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE utilisateur RENAME COLUMN hash_password TO password');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE patient ALTER sexe TYPE VARCHAR(10)');
        $this->addSql('ALTER TABLE patient ALTER groupe_sanguin TYPE VARCHAR(10)');
        $this->addSql('ALTER TABLE utilisateur ALTER statut TYPE BOOLEAN');
        $this->addSql('ALTER TABLE utilisateur RENAME COLUMN password TO hash_password');
    }
}
