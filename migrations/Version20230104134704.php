<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230104134704 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE status (id INT AUTO_INCREMENT NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE orders ADD id_user_id INT NOT NULL, ADD id_status_id INT NOT NULL');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE79F37AE5 FOREIGN KEY (id_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEEEBC2BC9A FOREIGN KEY (id_status_id) REFERENCES status (id)');
        $this->addSql('CREATE INDEX IDX_E52FFDEE79F37AE5 ON orders (id_user_id)');
        $this->addSql('CREATE INDEX IDX_E52FFDEEEBC2BC9A ON orders (id_status_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEEEBC2BC9A');
        $this->addSql('DROP TABLE status');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE79F37AE5');
        $this->addSql('DROP INDEX IDX_E52FFDEE79F37AE5 ON orders');
        $this->addSql('DROP INDEX IDX_E52FFDEEEBC2BC9A ON orders');
        $this->addSql('ALTER TABLE orders DROP id_user_id, DROP id_status_id');
    }
}
