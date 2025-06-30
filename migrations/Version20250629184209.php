<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250629184209 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add fulltext index';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE task_search_index ADD FULLTEXT fulltext_idx (content)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE task_search_index DROP INDEX fulltext_idx');
    }
}
