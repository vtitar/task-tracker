<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250622134727 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE task ADD parent_id INT DEFAULT NULL, ADD user_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE task ADD CONSTRAINT FK_527EDB25727ACA70 FOREIGN KEY (parent_id) REFERENCES task (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE task ADD CONSTRAINT FK_527EDB25A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_527EDB25727ACA70 ON task (parent_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_527EDB25A76ED395 ON task (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_527EDB2562A6DC27 ON task (priority)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_527EDB257B00651C ON task (status)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_527EDB258B8E8428 ON task (created_at)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_527EDB25540ED13A ON task (completed_at)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE task DROP FOREIGN KEY FK_527EDB25727ACA70
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE task DROP FOREIGN KEY FK_527EDB25A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_527EDB25727ACA70 ON task
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_527EDB25A76ED395 ON task
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_527EDB2562A6DC27 ON task
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_527EDB257B00651C ON task
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_527EDB258B8E8428 ON task
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_527EDB25540ED13A ON task
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE task DROP parent_id, DROP user_id
        SQL);
    }
}
