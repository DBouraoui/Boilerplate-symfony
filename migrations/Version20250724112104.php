<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250724112104 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE sessions_id_seq CASCADE');
        $this->addSql('CREATE TABLE session (id SERIAL NOT NULL, user_id_id INT NOT NULL, ip VARCHAR(50) NOT NULL, user_agent TEXT NOT NULL, logged_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D044D5D49D86650F ON session (user_id_id)');
        $this->addSql('COMMENT ON COLUMN session.logged_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D49D86650F FOREIGN KEY (user_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sessions DROP CONSTRAINT sessions_pkey');
        $this->addSql('ALTER TABLE sessions DROP id');
        $this->addSql('CREATE INDEX sess_lifetime_idx ON sessions (sess_lifetime)');
        $this->addSql('ALTER TABLE sessions ADD PRIMARY KEY (sess_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE sessions_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('ALTER TABLE session DROP CONSTRAINT FK_D044D5D49D86650F');
        $this->addSql('DROP TABLE session');
        $this->addSql('DROP INDEX sess_lifetime_idx');
        $this->addSql('DROP INDEX sessions_pkey');
        $this->addSql('ALTER TABLE sessions ADD id SERIAL NOT NULL');
        $this->addSql('ALTER TABLE sessions ADD PRIMARY KEY (id)');
    }
}
