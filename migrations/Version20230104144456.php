<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230104144456 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE batch ADD user_id INT NOT NULL, ADD product_id INT NOT NULL');
        $this->addSql('ALTER TABLE batch ADD CONSTRAINT FK_F80B52D4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE batch ADD CONSTRAINT FK_F80B52D44584665A FOREIGN KEY (product_id) REFERENCES products (id)');
        $this->addSql('CREATE INDEX IDX_F80B52D4A76ED395 ON batch (user_id)');
        $this->addSql('CREATE INDEX IDX_F80B52D44584665A ON batch (product_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE batch DROP FOREIGN KEY FK_F80B52D4A76ED395');
        $this->addSql('ALTER TABLE batch DROP FOREIGN KEY FK_F80B52D44584665A');
        $this->addSql('DROP INDEX IDX_F80B52D4A76ED395 ON batch');
        $this->addSql('DROP INDEX IDX_F80B52D44584665A ON batch');
        $this->addSql('ALTER TABLE batch DROP user_id, DROP product_id');
    }
}
