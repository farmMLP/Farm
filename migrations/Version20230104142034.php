<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230104142034 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE week (id INT AUTO_INCREMENT NOT NULL, day VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE health_center ADD user_id INT NOT NULL, ADD shipment_day_id INT NOT NULL');
        $this->addSql('ALTER TABLE health_center ADD CONSTRAINT FK_3B1A03A1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE health_center ADD CONSTRAINT FK_3B1A03A1AFF2DBE3 FOREIGN KEY (shipment_day_id) REFERENCES week (id)');
        $this->addSql('CREATE INDEX IDX_3B1A03A1A76ED395 ON health_center (user_id)');
        $this->addSql('CREATE INDEX IDX_3B1A03A1AFF2DBE3 ON health_center (shipment_day_id)');
        $this->addSql('ALTER TABLE medical_samples ADD health_center_id INT NOT NULL');
        $this->addSql('ALTER TABLE medical_samples ADD CONSTRAINT FK_5F90B7B968B458E5 FOREIGN KEY (health_center_id) REFERENCES health_center (id)');
        $this->addSql('CREATE INDEX IDX_5F90B7B968B458E5 ON medical_samples (health_center_id)');
        $this->addSql('ALTER TABLE orders ADD health_center_id INT NOT NULL');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE68B458E5 FOREIGN KEY (health_center_id) REFERENCES health_center (id)');
        $this->addSql('CREATE INDEX IDX_E52FFDEE68B458E5 ON orders (health_center_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE health_center DROP FOREIGN KEY FK_3B1A03A1AFF2DBE3');
        $this->addSql('DROP TABLE week');
        $this->addSql('ALTER TABLE health_center DROP FOREIGN KEY FK_3B1A03A1A76ED395');
        $this->addSql('DROP INDEX IDX_3B1A03A1A76ED395 ON health_center');
        $this->addSql('DROP INDEX IDX_3B1A03A1AFF2DBE3 ON health_center');
        $this->addSql('ALTER TABLE health_center DROP user_id, DROP shipment_day_id');
        $this->addSql('ALTER TABLE medical_samples DROP FOREIGN KEY FK_5F90B7B968B458E5');
        $this->addSql('DROP INDEX IDX_5F90B7B968B458E5 ON medical_samples');
        $this->addSql('ALTER TABLE medical_samples DROP health_center_id');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE68B458E5');
        $this->addSql('DROP INDEX IDX_E52FFDEE68B458E5 ON orders');
        $this->addSql('ALTER TABLE orders DROP health_center_id');
    }
}
