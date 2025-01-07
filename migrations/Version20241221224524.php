<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241221224524 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cart_item ADD cartorder_id INT DEFAULT NULL, ADD panier_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE cart_item ADD CONSTRAINT FK_F0FE252720D4442 FOREIGN KEY (cartorder_id) REFERENCES `order` (id)');
        $this->addSql('ALTER TABLE cart_item ADD CONSTRAINT FK_F0FE2527F77D927C FOREIGN KEY (panier_id) REFERENCES `order` (id)');
        $this->addSql('CREATE INDEX IDX_F0FE252720D4442 ON cart_item (cartorder_id)');
        $this->addSql('CREATE INDEX IDX_F0FE2527F77D927C ON cart_item (panier_id)');
        $this->addSql('ALTER TABLE `order` ADD shipping_adress VARCHAR(255) NOT NULL, ADD payment_method VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cart_item DROP FOREIGN KEY FK_F0FE252720D4442');
        $this->addSql('ALTER TABLE cart_item DROP FOREIGN KEY FK_F0FE2527F77D927C');
        $this->addSql('DROP INDEX IDX_F0FE252720D4442 ON cart_item');
        $this->addSql('DROP INDEX IDX_F0FE2527F77D927C ON cart_item');
        $this->addSql('ALTER TABLE cart_item DROP cartorder_id, DROP panier_id');
        $this->addSql('ALTER TABLE `order` DROP shipping_adress, DROP payment_method');
    }
}
