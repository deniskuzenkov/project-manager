<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240204153944 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_user_networks ALTER user_id TYPE UUID');
        $this->addSql('ALTER TABLE user_users ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE user_users ALTER email TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE user_users ALTER role TYPE VARCHAR(16)');
        $this->addSql('ALTER TABLE user_users ALTER new_email TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE work_members_group ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE work_members_members ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE work_members_members ALTER group_id TYPE UUID');
        $this->addSql('ALTER TABLE work_members_members ALTER email TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE work_members_members ALTER status TYPE VARCHAR(16)');
        $this->addSql('ALTER TABLE work_projects_projects ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE work_projects_projects ALTER status TYPE VARCHAR(16)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE work_members_members ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE work_members_members ALTER group_id TYPE UUID');
        $this->addSql('ALTER TABLE work_members_members ALTER email TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE work_members_members ALTER status TYPE VARCHAR(16)');
        $this->addSql('ALTER TABLE work_members_group ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE work_projects_projects ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE work_projects_projects ALTER status TYPE VARCHAR(16)');
        $this->addSql('ALTER TABLE user_users ALTER id TYPE UUID');
        $this->addSql('ALTER TABLE user_users ALTER email TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE user_users ALTER new_email TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE user_users ALTER role TYPE VARCHAR(16)');
        $this->addSql('ALTER TABLE user_user_networks ALTER user_id TYPE UUID');
    }
}
