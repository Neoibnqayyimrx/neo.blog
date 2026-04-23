-- SQLite schema + seed data — mirrors schema.mysql.sql but in SQLite dialect.
-- Usage: sqlite3 storage/database.sqlite < database/schema.sqlite.sql

PRAGMA foreign_keys = ON;

DROP TABLE IF EXISTS posts;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    firstname   TEXT NOT NULL,
    lastname    TEXT NOT NULL,
    username    TEXT NOT NULL UNIQUE,
    email       TEXT NOT NULL UNIQUE,
    password    TEXT NOT NULL,
    avatar      TEXT NOT NULL DEFAULT 'default-avatar.png',
    is_admin    INTEGER NOT NULL DEFAULT 0,
    created_at  TEXT NOT NULL DEFAULT (strftime('%Y-%m-%d %H:%M:%S','now'))
);

CREATE TABLE categories (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    title       TEXT NOT NULL UNIQUE,
    created_at  TEXT NOT NULL DEFAULT (strftime('%Y-%m-%d %H:%M:%S','now'))
);

CREATE TABLE posts (
    id           INTEGER PRIMARY KEY AUTOINCREMENT,
    title        TEXT NOT NULL,
    body         TEXT NOT NULL,
    thumbnail    TEXT NOT NULL DEFAULT 'default-thumbnail.png',
    category_id  INTEGER NOT NULL,
    author_id    INTEGER NOT NULL,
    is_featured  INTEGER NOT NULL DEFAULT 0,
    date_time    TEXT NOT NULL DEFAULT (strftime('%Y-%m-%d %H:%M:%S','now')),
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT,
    FOREIGN KEY (author_id)   REFERENCES users(id)      ON DELETE CASCADE
);

-- Seed (passwords: admin/Admin1234!, johndoe/Password1!)
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
   'This is the first story published on the platform. NEO · BLOG is built around the belief that good writing deserves good typography, intentional layout, and a publishing workflow that stays out of the author''s way.' || char(10) || char(10) ||
   'Browse the categories, read a story, and — if you are an administrator — head to the dashboard to publish your own.',
   'default-thumbnail.png', 1, 1, 1),

  ('How we think about images',
   'Images carry half the weight of a post. Every thumbnail uploaded to NEO · BLOG is cropped to a 16:10 aspect ratio on the card and a cinematic 21:9 at the top of the article.',
   'default-thumbnail.png', 2, 1, 0),

  ('An opinionated tour of the admin dashboard',
   'The admin dashboard is the heart of the platform. The sidebar is sticky, clearly grouped, and collapses into a slide-out on narrow screens.',
   'default-thumbnail.png', 5, 1, 0),

  ('A short meditation on craft',
   'Software is a craft, not only in how it''s written but in how its seams are finished. The corners the user will never see are still part of the whole.',
   'default-thumbnail.png', 3, 2, 0),

  ('Why we picked Inter and Playfair',
   'NEO · BLOG pairs Inter (body, UI) with Playfair Display (headings). Inter is a workhorse; Playfair adds just enough editorial personality.',
   'default-thumbnail.png', 2, 2, 0),

  ('Engineering for change',
   'Under the hood, NEO · BLOG is a small PHP MVC application. The Database class speaks PDO and works against MySQL or SQLite. Models are thin wrappers over prepared statements.',
   'default-thumbnail.png', 5, 1, 0);
