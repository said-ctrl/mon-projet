<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241221230202 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cart_item DROP FOREIGN KEY FK_F0FE2527F77D927C');
        $this->addSql('DROP INDEX IDX_F0FE2527F77D927C ON cart_item');
        $this->addSql('ALTER TABLE cart_item DROP panier_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cart_item ADD panier_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE cart_item ADD CONSTRAINT FK_F0FE2527F77D927C FOREIGN KEY (panier_id) REFERENCES `order` (id)');
        $this->addSql('CREATE INDEX IDX_F0FE2527F77D927C ON cart_item (panier_id)');
    }
}
