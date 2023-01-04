<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230104143643 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE products_by_order ADD product_id INT NOT NULL, ADD petition_id INT NOT NULL');
        $this->addSql('ALTER TABLE products_by_order ADD CONSTRAINT FK_55CE23434584665A FOREIGN KEY (product_id) REFERENCES products (id)');
        $this->addSql('ALTER TABLE products_by_order ADD CONSTRAINT FK_55CE2343AEC7D346 FOREIGN KEY (petition_id) REFERENCES orders (id)');
        $this->addSql('CREATE INDEX IDX_55CE23434584665A ON products_by_order (product_id)');
        $this->addSql('CREATE INDEX IDX_55CE2343AEC7D346 ON products_by_order (petition_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE products_by_order DROP FOREIGN KEY FK_55CE23434584665A');
        $this->addSql('ALTER TABLE products_by_order DROP FOREIGN KEY FK_55CE2343AEC7D346');
        $this->addSql('DROP INDEX IDX_55CE23434584665A ON products_by_order');
        $this->addSql('DROP INDEX IDX_55CE2343AEC7D346 ON products_by_order');
        $this->addSql('ALTER TABLE products_by_order DROP product_id, DROP petition_id');
    }
}
