<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210514133446 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `signal` DROP FOREIGN KEY FK_740C95F54B89032C');
        $this->addSql('DROP INDEX IDX_740C95F54B89032C ON `signal`');
        $this->addSql('ALTER TABLE `signal` DROP post_id, CHANGE message_id message_id INT NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `signal` ADD post_id INT DEFAULT NULL, CHANGE message_id message_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE `signal` ADD CONSTRAINT FK_740C95F54B89032C FOREIGN KEY (post_id) REFERENCES post (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_740C95F54B89032C ON `signal` (post_id)');
    }
}
