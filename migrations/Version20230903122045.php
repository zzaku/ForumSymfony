<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230903122045 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE board DROP FOREIGN KEY FK_58562B4712469DE2');
        $this->addSql('ALTER TABLE topic DROP FOREIGN KEY FK_9D40DE1BE7EC5785');
        $this->addSql('ALTER TABLE file DROP FOREIGN KEY FK_8C9F3610537A1329');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F1F55203D');
        $this->addSql('DROP TABLE board');
        $this->addSql('DROP TABLE topic');
        $this->addSql('DROP TABLE file');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE category');
        $this->addSql('ALTER TABLE user ADD roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', DROP role');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE board (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci`, INDEX IDX_58562B4712469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb3 COLLATE `utf8mb3_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE topic (id INT AUTO_INCREMENT NOT NULL, board_id INT NOT NULL, title VARCHAR(255) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci`, created_at DATETIME NOT NULL, INDEX IDX_9D40DE1BE7EC5785 (board_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb3 COLLATE `utf8mb3_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE file (id INT AUTO_INCREMENT NOT NULL, message_id INT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci`, path VARCHAR(255) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci`, INDEX IDX_8C9F3610537A1329 (message_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb3 COLLATE `utf8mb3_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, topic_id INT NOT NULL, content LONGTEXT CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci`, created_at DATETIME NOT NULL, INDEX IDX_B6BD307F1F55203D (topic_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb3 COLLATE `utf8mb3_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb3 NOT NULL COLLATE `utf8mb3_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb3 COLLATE `utf8mb3_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE board ADD CONSTRAINT FK_58562B4712469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE topic ADD CONSTRAINT FK_9D40DE1BE7EC5785 FOREIGN KEY (board_id) REFERENCES board (id)');
        $this->addSql('ALTER TABLE file ADD CONSTRAINT FK_8C9F3610537A1329 FOREIGN KEY (message_id) REFERENCES message (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F1F55203D FOREIGN KEY (topic_id) REFERENCES topic (id)');
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74 ON user');
        $this->addSql('ALTER TABLE user ADD role VARCHAR(50) NOT NULL, DROP roles');
    }
}
