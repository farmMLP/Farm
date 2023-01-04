<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230104135706 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE79F37AE5');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEEEBC2BC9A');
        $this->addSql('DROP INDEX IDX_E52FFDEEEBC2BC9A ON orders');
        $this->addSql('DROP INDEX IDX_E52FFDEE79F37AE5 ON orders');
        $this->addSql('ALTER TABLE orders ADD user_id INT NOT NULL, ADD status_id INT NOT NULL, DROP id_user_id, DROP id_status_id');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE6BF700BD FOREIGN KEY (status_id) REFERENCES status (id)');
        $this->addSql('CREATE INDEX IDX_E52FFDEEA76ED395 ON orders (user_id)');
        $this->addSql('CREATE INDEX IDX_E52FFDEE6BF700BD ON orders (status_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEEA76ED395');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE6BF700BD');
        $this->addSql('DROP INDEX IDX_E52FFDEEA76ED395 ON orders');
        $this->addSql('DROP INDEX IDX_E52FFDEE6BF700BD ON orders');
        $this->addSql('ALTER TABLE orders ADD id_user_id INT NOT NULL, ADD id_status_id INT NOT NULL, DROP user_id, DROP status_id');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE79F37AE5 FOREIGN KEY (id_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEEEBC2BC9A FOREIGN KEY (id_status_id) REFERENCES status (id)');
        $this->addSql('CREATE INDEX IDX_E52FFDEEEBC2BC9A ON orders (id_status_id)');
        $this->addSql('CREATE INDEX IDX_E52FFDEE79F37AE5 ON orders (id_user_id)');
    }
}
