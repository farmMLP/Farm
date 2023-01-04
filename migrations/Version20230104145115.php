<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230104145115 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE medical_samples ADD product_id INT NOT NULL');
        $this->addSql('ALTER TABLE medical_samples ADD CONSTRAINT FK_5F90B7B94584665A FOREIGN KEY (product_id) REFERENCES products (id)');
        $this->addSql('CREATE INDEX IDX_5F90B7B94584665A ON medical_samples (product_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE medical_samples DROP FOREIGN KEY FK_5F90B7B94584665A');
        $this->addSql('DROP INDEX IDX_5F90B7B94584665A ON medical_samples');
        $this->addSql('ALTER TABLE medical_samples DROP product_id');
    }
}
