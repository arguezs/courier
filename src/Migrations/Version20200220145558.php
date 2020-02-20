<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200220145558 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE inbox_message');
        $this->addSql('ALTER TABLE inbox DROP FOREIGN KEY FK_7E11F3397E3C61F9');
        $this->addSql('DROP INDEX IDX_7E11F3397E3C61F9 ON inbox');
        $this->addSql('ALTER TABLE inbox ADD message_id INT NOT NULL, ADD is_read TINYINT(1) NOT NULL, CHANGE owner_id user_id INT NOT NULL');
        $this->addSql('ALTER TABLE inbox ADD CONSTRAINT FK_7E11F339A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE inbox ADD CONSTRAINT FK_7E11F339537A1329 FOREIGN KEY (message_id) REFERENCES message (id)');
        $this->addSql('CREATE INDEX IDX_7E11F339A76ED395 ON inbox (user_id)');
        $this->addSql('CREATE INDEX IDX_7E11F339537A1329 ON inbox (message_id)');
        $this->addSql('ALTER TABLE message CHANGE responses_id responses_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL, CHANGE recovery recovery VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE inbox_message (inbox_id INT NOT NULL, message_id INT NOT NULL, INDEX IDX_8389368018DA89DD (inbox_id), INDEX IDX_83893680537A1329 (message_id), PRIMARY KEY(inbox_id, message_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE inbox_message ADD CONSTRAINT FK_8389368018DA89DD FOREIGN KEY (inbox_id) REFERENCES inbox (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE inbox_message ADD CONSTRAINT FK_83893680537A1329 FOREIGN KEY (message_id) REFERENCES message (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE inbox DROP FOREIGN KEY FK_7E11F339A76ED395');
        $this->addSql('ALTER TABLE inbox DROP FOREIGN KEY FK_7E11F339537A1329');
        $this->addSql('DROP INDEX IDX_7E11F339A76ED395 ON inbox');
        $this->addSql('DROP INDEX IDX_7E11F339537A1329 ON inbox');
        $this->addSql('ALTER TABLE inbox ADD owner_id INT NOT NULL, DROP user_id, DROP message_id, DROP is_read');
        $this->addSql('ALTER TABLE inbox ADD CONSTRAINT FK_7E11F3397E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_7E11F3397E3C61F9 ON inbox (owner_id)');
        $this->addSql('ALTER TABLE message CHANGE responses_id responses_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_bin`, CHANGE recovery recovery VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT \'NULL\' COLLATE `utf8mb4_unicode_ci`');
    }
}
