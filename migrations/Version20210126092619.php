<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210126092619 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE subreddit_user (subreddit_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_78A09F1831DBE174 (subreddit_id), INDEX IDX_78A09F18A76ED395 (user_id), PRIMARY KEY(subreddit_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE subreddit_user ADD CONSTRAINT FK_78A09F1831DBE174 FOREIGN KEY (subreddit_id) REFERENCES subreddit (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE subreddit_user ADD CONSTRAINT FK_78A09F18A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE subreddit_user');
    }
}
