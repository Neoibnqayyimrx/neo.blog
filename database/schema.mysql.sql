-- MySQL schema + seed data for NEO · BLOG
-- Load with:  mysql -u root -p php_blog < database/schema.mysql.sql

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS posts;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE users (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    firstname   VARCHAR(100) NOT NULL,
    lastname    VARCHAR(100) NOT NULL,
    username    VARCHAR(50)  NOT NULL,
    email       VARCHAR(190) NOT NULL,
    password    VARCHAR(255) NOT NULL,
    avatar      VARCHAR(255) NOT NULL DEFAULT 'default-avatar.png',
    is_admin    TINYINT(1)   NOT NULL DEFAULT 0,
    created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_username (username),
    UNIQUE KEY uniq_email    (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE categories (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(120) NOT NULL,
    created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_title (title)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE posts (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title        VARCHAR(255) NOT NULL,
    body         LONGTEXT     NOT NULL,
    thumbnail    VARCHAR(255) NOT NULL DEFAULT 'default-thumbnail.png',
    category_id  INT UNSIGNED NOT NULL,
    author_id    INT UNSIGNED NOT NULL,
    is_featured  TINYINT(1)   NOT NULL DEFAULT 0,
    date_time    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_category (category_id),
    KEY idx_author   (author_id),
    CONSTRAINT fk_posts_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT,
    CONSTRAINT fk_posts_author   FOREIGN KEY (author_id)   REFERENCES users(id)      ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ────────────────────────────────────────────────────────────
-- Seed data
-- Passwords below are bcrypt hashes. Plaintext:
--   admin    -> Admin1234!
--   johndoe  -> Password1!
-- ────────────────────────────────────────────────────────────
INSERT INTO users (firstname, lastname, username, email, password, avatar, is_admin) VALUES
  ('Ada',  'Lovelace', 'admin',   'admin@example.com',
   '$2y$12$PKkjQK72VjrzKmnJ80cAbelcChXI4v3LJoBgjVXsVko2VNrVrdrjm',
   'default-avatar.png', 1),
  ('John', 'Doe',      'johndoe', 'john@example.com',
   '$2y$12$r/IFx3xMgRcR7CkHhLjaa.7uqkGXZqiMXkI070D2IgTFMnKmQNcpO',
   'default-avatar.png', 0);

INSERT INTO categories (title) VALUES
  ('Technology'),
  ('Design'),
  ('Philosophy'),
  ('Culture'),
  ('Engineering');

INSERT INTO posts (title, body, thumbnail, category_id, author_id, is_featured) VALUES
  ('Welcome to NEO · BLOG',
   'This is the first story published on the platform. NEO · BLOG is built around the belief that good writing deserves good typography, intentional layout, and a publishing workflow that stays out of the author''s way.\n\nBrowse the categories, read a story, and — if you are an administrator — head to the dashboard to publish your own.',
   'default-thumbnail.png', 1, 1, 1),

  ('How we think about images',
   'Images carry half the weight of a post. Every thumbnail uploaded to NEO · BLOG is cropped to a 16:10 aspect ratio on the card and a cinematic 21:9 at the top of the article. The upload handler validates extension and MIME type, strips filenames to a random prefix, and rejects anything larger than 2 MB.\n\nOn the article page, images render at full width with a soft elevation, but never exceed the reading column.',
   'default-thumbnail.png', 2, 1, 0),

  ('An opinionated tour of the admin dashboard',
   'The admin dashboard is the heart of the platform. The sidebar is sticky, clearly grouped (Main / Content / Site), and collapses into a slide-out on screens narrower than 1024px. The top bar carries breadcrumbs and primary actions.\n\nTables use uppercase column headings, soft row hover states, and action icons with generous tap targets. Destructive actions (delete, revoke-admin) are always POST forms carrying a CSRF token.',
   'default-thumbnail.png', 5, 1, 0),

  ('A short meditation on craft',
   'Software is a craft, not only in how it''s written but in how its seams are finished. The corners the user will never see are still part of the whole. A delete confirmation phrased carefully. A form that remembers what you typed when it fails. A 404 page that doesn''t apologise, but gently points home.',
   'default-thumbnail.png', 3, 2, 0),

  ('Why we picked Inter and Playfair',
   'NEO · BLOG pairs Inter (body, UI) with Playfair Display (headings). Inter is a workhorse: neutral, great at small sizes, available in many weights. Playfair adds just enough editorial personality to a title without shouting.\n\nWe avoid mixing more than two families — every extra typeface is a surface where subtle mistakes accumulate.',
   'default-thumbnail.png', 2, 2, 0),

  ('Engineering for change',
   'Under the hood, NEO · BLOG is a small PHP MVC application. The Database class speaks PDO and works against MySQL in production or SQLite during development. Models are thin wrappers over prepared statements. Controllers are small classes with one method per route. Views extend a layout and expose only the variables they need.',
   'default-thumbnail.png', 5, 1, 0);
