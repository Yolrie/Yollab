<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260325000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Feature B — User, Snippet, Tag, Review, LineComment';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE "user" (
            id                SERIAL PRIMARY KEY,
            email             VARCHAR(180) NOT NULL,
            username          VARCHAR(50)  NOT NULL,
            roles             JSON         NOT NULL DEFAULT \'[]\',
            password          VARCHAR(255) NOT NULL,
            reputation_score  INT          NOT NULL DEFAULT 0,
            created_at        TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL,
            CONSTRAINT uq_user_email    UNIQUE (email),
            CONSTRAINT uq_user_username UNIQUE (username)
        )');

        $this->addSql('CREATE TABLE snippet (
            id          SERIAL PRIMARY KEY,
            author_id   INT          NOT NULL REFERENCES "user"(id),
            title       VARCHAR(255) NOT NULL,
            language    VARCHAR(50)  NOT NULL DEFAULT \'plaintext\',
            code        TEXT         NOT NULL,
            status      VARCHAR(20)  NOT NULL DEFAULT \'pending\',
            created_at  TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL
        )');
        $this->addSql('CREATE INDEX idx_snippet_author    ON snippet (author_id)');
        $this->addSql('CREATE INDEX idx_snippet_language  ON snippet (language)');
        $this->addSql('CREATE INDEX idx_snippet_status    ON snippet (status)');
        $this->addSql('CREATE INDEX idx_snippet_created   ON snippet (created_at DESC)');

        $this->addSql('CREATE TABLE tag (
            id    SERIAL PRIMARY KEY,
            name  VARCHAR(50) NOT NULL,
            CONSTRAINT uq_tag_name UNIQUE (name)
        )');

        $this->addSql('CREATE TABLE snippet_tag (
            snippet_id INT NOT NULL REFERENCES snippet(id) ON DELETE CASCADE,
            tag_id     INT NOT NULL REFERENCES tag(id)     ON DELETE CASCADE,
            PRIMARY KEY (snippet_id, tag_id)
        )');

        $this->addSql('CREATE TABLE review (
            id          SERIAL PRIMARY KEY,
            snippet_id  INT          NOT NULL REFERENCES snippet(id) ON DELETE CASCADE,
            reviewer_id INT          NOT NULL REFERENCES "user"(id),
            comment     TEXT         NOT NULL,
            status      VARCHAR(20)  NOT NULL DEFAULT \'needs_work\',
            created_at  TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL
        )');
        $this->addSql('CREATE INDEX idx_review_snippet  ON review (snippet_id)');
        $this->addSql('CREATE INDEX idx_review_reviewer ON review (reviewer_id)');

        $this->addSql('CREATE TABLE line_comment (
            id          SERIAL PRIMARY KEY,
            snippet_id  INT  NOT NULL REFERENCES snippet(id) ON DELETE CASCADE,
            author_id   INT  NOT NULL REFERENCES "user"(id),
            line_number INT  NOT NULL,
            content     TEXT NOT NULL,
            created_at  TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL
        )');
        $this->addSql('CREATE INDEX idx_lc_snippet ON line_comment (snippet_id)');
        $this->addSql('CREATE INDEX idx_lc_line    ON line_comment (snippet_id, line_number)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE line_comment');
        $this->addSql('DROP TABLE review');
        $this->addSql('DROP TABLE snippet_tag');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE snippet');
        $this->addSql('DROP TABLE "user"');
    }
}
