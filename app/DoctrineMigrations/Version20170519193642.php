<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20170519193642 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE show_schedule_meta (id BIGINT AUTO_INCREMENT NOT NULL, show_schedule_id BIGINT DEFAULT NULL, meta_key VARCHAR(20) DEFAULT NULL, meta_value BIGINT DEFAULT NULL, INDEX IDX_B2A7AC7BB0F329D (show_schedule_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comment (id BIGINT AUTO_INCREMENT NOT NULL, comment_id BIGINT DEFAULT NULL, user_id BIGINT DEFAULT NULL, created_at DATETIME DEFAULT NULL, update_at DATETIME DEFAULT NULL, content TEXT DEFAULT NULL, INDEX comment_comment_id_id_fk (comment_id), INDEX comment_user_id_fk (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image (id BIGINT AUTO_INCREMENT NOT NULL, source VARCHAR(200) NOT NULL, mimetype VARCHAR(30) DEFAULT NULL, created_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE blog (id BIGINT AUTO_INCREMENT NOT NULL, author_id BIGINT DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, category VARCHAR(60) DEFAULT NULL, post_excerpt TEXT DEFAULT NULL, status VARCHAR(40) DEFAULT NULL, is_pinned TINYINT(1) DEFAULT NULL, content BLOB DEFAULT NULL, INDEX blog_user_id_fk (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE blog_image (blog_id BIGINT NOT NULL, image_id BIGINT NOT NULL, INDEX IDX_35D24797DAE07E97 (blog_id), UNIQUE INDEX UNIQ_35D247973DA5256D (image_id), PRIMARY KEY(blog_id, image_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE blog_comment (blog_id BIGINT NOT NULL, comment_id BIGINT NOT NULL, INDEX IDX_7882EFEFDAE07E97 (blog_id), UNIQUE INDEX UNIQ_7882EFEFF8697D13 (comment_id), PRIMARY KEY(blog_id, comment_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recording (id BIGINT AUTO_INCREMENT NOT NULL, event_id BIGINT DEFAULT NULL, show_id BIGINT DEFAULT NULL, source VARCHAR(100) DEFAULT NULL, short_name VARCHAR(80) DEFAULT NULL, downloads INT DEFAULT NULL, created_on DATETIME DEFAULT NULL, description TEXT DEFAULT NULL, INDEX recording_event_id_fk (event_id), INDEX recording_show_id_fk (show_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id BIGINT AUTO_INCREMENT NOT NULL, facebook_id BIGINT DEFAULT NULL, email VARCHAR(100) NOT NULL, student_id VARCHAR(15) NOT NULL, phone VARCHAR(30) DEFAULT NULL, name VARCHAR(120) NOT NULL, last_login DATETIME DEFAULT NULL, password VARCHAR(200) NOT NULL, suspended TINYINT(1) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, username VARCHAR(30) DEFAULT NULL, confirmed TINYINT(1) DEFAULT NULL, confirmation_token VARCHAR(30) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event (id BIGINT AUTO_INCREMENT NOT NULL, show_id BIGINT DEFAULT NULL, show_schedule_id BIGINT DEFAULT NULL, start DATETIME DEFAULT NULL, end DATETIME DEFAULT NULL, INDEX _event_show_id_fk (show_id), INDEX _event_show_schedule_id_fk (show_schedule_id), UNIQUE INDEX event_id_uindex (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dj (id BIGINT AUTO_INCREMENT NOT NULL, user_id BIGINT DEFAULT NULL, description BLOB DEFAULT NULL, strike_count INT DEFAULT NULL, attend_workshop TINYINT(1) DEFAULT NULL, UNIQUE INDEX dj_user_id_uindex (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dj_image (dj_id BIGINT NOT NULL, image_id BIGINT NOT NULL, INDEX IDX_E0822BE3670B2DD5 (dj_id), UNIQUE INDEX UNIQ_E0822BE33DA5256D (image_id), PRIMARY KEY(dj_id, image_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE show_dj (show_id BIGINT NOT NULL, dj_id BIGINT NOT NULL, INDEX IDX_82D7AF2ED0C1FC64 (show_id), INDEX IDX_82D7AF2E670B2DD5 (dj_id), PRIMARY KEY(show_id, dj_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE attendance (id BIGINT AUTO_INCREMENT NOT NULL, event_id BIGINT DEFAULT NULL, strike_id BIGINT DEFAULT NULL, status VARCHAR(20) DEFAULT NULL, late TIME DEFAULT NULL, created_on DATETIME DEFAULT NULL, INDEX attendance_event_id_fk (event_id), INDEX attendance_strike_id_fk (strike_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE show_schedule (id BIGINT AUTO_INCREMENT NOT NULL, show_id BIGINT DEFAULT NULL, start_time TIME DEFAULT NULL, end_time TIME DEFAULT NULL, INDEX IDX_635CF014D0C1FC64 (show_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE staff (id BIGINT AUTO_INCREMENT NOT NULL, user_id BIGINT DEFAULT NULL, UNIQUE INDEX UNIQ_426EF392A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE shows (id BIGINT AUTO_INCREMENT NOT NULL, header_image_id BIGINT DEFAULT NULL, name VARCHAR(100) NOT NULL, description BLOB NOT NULL, created_at DATETIME NOT NULL, score INT NOT NULL, profanity TINYINT(1) NOT NULL, attendance_optional TINYINT(1) NOT NULL, updated_at DATETIME DEFAULT NULL, genre VARCHAR(80) DEFAULT NULL, strike_count INT DEFAULT NULL, suspended TINYINT(1) DEFAULT NULL, enable_comments TINYINT(1) DEFAULT NULL, UNIQUE INDEX UNIQ_6C3BF1448C782417 (header_image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE show_image (show_id BIGINT NOT NULL, image_id BIGINT NOT NULL, INDEX IDX_C78C6083D0C1FC64 (show_id), UNIQUE INDEX UNIQ_C78C60833DA5256D (image_id), PRIMARY KEY(show_id, image_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE show_comment (show_id BIGINT NOT NULL, comment_id BIGINT NOT NULL, INDEX IDX_F3267F8FD0C1FC64 (show_id), UNIQUE INDEX UNIQ_F3267F8FF8697D13 (comment_id), PRIMARY KEY(show_id, comment_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE strike (id BIGINT AUTO_INCREMENT NOT NULL, user_id BIGINT DEFAULT NULL, created_on DATETIME DEFAULT NULL, reason TEXT DEFAULT NULL, email_student TINYINT(1) DEFAULT NULL, type VARCHAR(20) DEFAULT NULL, INDEX strike_user_id_fk (user_id), UNIQUE INDEX strike_id_uindex (id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE show_schedule_meta ADD CONSTRAINT FK_B2A7AC7BB0F329D FOREIGN KEY (show_schedule_id) REFERENCES show_schedule (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CF8697D13 FOREIGN KEY (comment_id) REFERENCES comment (id)');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE blog ADD CONSTRAINT FK_C0155143F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE blog_image ADD CONSTRAINT FK_35D24797DAE07E97 FOREIGN KEY (blog_id) REFERENCES blog (id)');
        $this->addSql('ALTER TABLE blog_image ADD CONSTRAINT FK_35D247973DA5256D FOREIGN KEY (image_id) REFERENCES image (id)');
        $this->addSql('ALTER TABLE blog_comment ADD CONSTRAINT FK_7882EFEFDAE07E97 FOREIGN KEY (blog_id) REFERENCES blog (id)');
        $this->addSql('ALTER TABLE blog_comment ADD CONSTRAINT FK_7882EFEFF8697D13 FOREIGN KEY (comment_id) REFERENCES comment (id)');
        $this->addSql('ALTER TABLE recording ADD CONSTRAINT FK_BB532B5371F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE recording ADD CONSTRAINT FK_BB532B53D0C1FC64 FOREIGN KEY (show_id) REFERENCES shows (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7D0C1FC64 FOREIGN KEY (show_id) REFERENCES shows (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7B0F329D FOREIGN KEY (show_schedule_id) REFERENCES show_schedule (id)');
        $this->addSql('ALTER TABLE dj ADD CONSTRAINT FK_ED2F341AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE dj_image ADD CONSTRAINT FK_E0822BE3670B2DD5 FOREIGN KEY (dj_id) REFERENCES dj (id)');
        $this->addSql('ALTER TABLE dj_image ADD CONSTRAINT FK_E0822BE33DA5256D FOREIGN KEY (image_id) REFERENCES image (id)');
        $this->addSql('ALTER TABLE show_dj ADD CONSTRAINT FK_82D7AF2ED0C1FC64 FOREIGN KEY (show_id) REFERENCES shows (id)');
        $this->addSql('ALTER TABLE show_dj ADD CONSTRAINT FK_82D7AF2E670B2DD5 FOREIGN KEY (dj_id) REFERENCES dj (id)');
        $this->addSql('ALTER TABLE attendance ADD CONSTRAINT FK_6DE30D9171F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE attendance ADD CONSTRAINT FK_6DE30D91282BB60B FOREIGN KEY (strike_id) REFERENCES strike (id)');
        $this->addSql('ALTER TABLE show_schedule ADD CONSTRAINT FK_635CF014D0C1FC64 FOREIGN KEY (show_id) REFERENCES shows (id)');
        $this->addSql('ALTER TABLE staff ADD CONSTRAINT FK_426EF392A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE shows ADD CONSTRAINT FK_6C3BF1448C782417 FOREIGN KEY (header_image_id) REFERENCES image (id)');
        $this->addSql('ALTER TABLE show_image ADD CONSTRAINT FK_C78C6083D0C1FC64 FOREIGN KEY (show_id) REFERENCES shows (id)');
        $this->addSql('ALTER TABLE show_image ADD CONSTRAINT FK_C78C60833DA5256D FOREIGN KEY (image_id) REFERENCES image (id)');
        $this->addSql('ALTER TABLE show_comment ADD CONSTRAINT FK_F3267F8FD0C1FC64 FOREIGN KEY (show_id) REFERENCES shows (id)');
        $this->addSql('ALTER TABLE show_comment ADD CONSTRAINT FK_F3267F8FF8697D13 FOREIGN KEY (comment_id) REFERENCES comment (id)');
        $this->addSql('ALTER TABLE strike ADD CONSTRAINT FK_DC727C0A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CF8697D13');
        $this->addSql('ALTER TABLE blog_comment DROP FOREIGN KEY FK_7882EFEFF8697D13');
        $this->addSql('ALTER TABLE show_comment DROP FOREIGN KEY FK_F3267F8FF8697D13');
        $this->addSql('ALTER TABLE blog_image DROP FOREIGN KEY FK_35D247973DA5256D');
        $this->addSql('ALTER TABLE dj_image DROP FOREIGN KEY FK_E0822BE33DA5256D');
        $this->addSql('ALTER TABLE shows DROP FOREIGN KEY FK_6C3BF1448C782417');
        $this->addSql('ALTER TABLE show_image DROP FOREIGN KEY FK_C78C60833DA5256D');
        $this->addSql('ALTER TABLE blog_image DROP FOREIGN KEY FK_35D24797DAE07E97');
        $this->addSql('ALTER TABLE blog_comment DROP FOREIGN KEY FK_7882EFEFDAE07E97');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526CA76ED395');
        $this->addSql('ALTER TABLE blog DROP FOREIGN KEY FK_C0155143F675F31B');
        $this->addSql('ALTER TABLE dj DROP FOREIGN KEY FK_ED2F341AA76ED395');
        $this->addSql('ALTER TABLE staff DROP FOREIGN KEY FK_426EF392A76ED395');
        $this->addSql('ALTER TABLE strike DROP FOREIGN KEY FK_DC727C0A76ED395');
        $this->addSql('ALTER TABLE recording DROP FOREIGN KEY FK_BB532B5371F7E88B');
        $this->addSql('ALTER TABLE attendance DROP FOREIGN KEY FK_6DE30D9171F7E88B');
        $this->addSql('ALTER TABLE dj_image DROP FOREIGN KEY FK_E0822BE3670B2DD5');
        $this->addSql('ALTER TABLE show_dj DROP FOREIGN KEY FK_82D7AF2E670B2DD5');
        $this->addSql('ALTER TABLE show_schedule_meta DROP FOREIGN KEY FK_B2A7AC7BB0F329D');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7B0F329D');
        $this->addSql('ALTER TABLE recording DROP FOREIGN KEY FK_BB532B53D0C1FC64');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7D0C1FC64');
        $this->addSql('ALTER TABLE show_dj DROP FOREIGN KEY FK_82D7AF2ED0C1FC64');
        $this->addSql('ALTER TABLE show_schedule DROP FOREIGN KEY FK_635CF014D0C1FC64');
        $this->addSql('ALTER TABLE show_image DROP FOREIGN KEY FK_C78C6083D0C1FC64');
        $this->addSql('ALTER TABLE show_comment DROP FOREIGN KEY FK_F3267F8FD0C1FC64');
        $this->addSql('ALTER TABLE attendance DROP FOREIGN KEY FK_6DE30D91282BB60B');
        $this->addSql('DROP TABLE show_schedule_meta');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE blog');
        $this->addSql('DROP TABLE blog_image');
        $this->addSql('DROP TABLE blog_comment');
        $this->addSql('DROP TABLE recording');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE dj');
        $this->addSql('DROP TABLE dj_image');
        $this->addSql('DROP TABLE show_dj');
        $this->addSql('DROP TABLE attendance');
        $this->addSql('DROP TABLE show_schedule');
        $this->addSql('DROP TABLE staff');
        $this->addSql('DROP TABLE shows');
        $this->addSql('DROP TABLE show_image');
        $this->addSql('DROP TABLE show_comment');
        $this->addSql('DROP TABLE strike');
    }
}
