# NEO · BLOG

A modern, production-ready PHP blog + admin dashboard. Refactored into a clean MVC-like architecture with a redesigned UI/UX, strong security defaults (prepared statements, bcrypt, CSRF, hardened upload handling), and two zero-friction ways to run it locally.

---

## Table of contents

1. [Highlights](#highlights)
2. [Project structure](#project-structure)
3. [Running locally](#running-locally)
    - [Option A — Docker (MySQL, mirrors production)](#option-a--docker-mysql-mirrors-production)
    - [Option B — PHP built-in server + SQLite (no install)](#option-b--php-built-in-server--sqlite-no-install)
    - [Option C — Classic LAMP (XAMPP / MAMP)](#option-c--classic-lamp-xampp--mamp)
4. [Seed accounts](#seed-accounts)
5. [Design system](#design-system)
6. [What was improved / fixed](#what-was-improved--fixed)
7. [Assumptions](#assumptions)
8. [License](#license)

---

## Highlights

- **MVC-ish architecture** — tiny router, thin controllers, testable models, reusable view layouts and partials.
- **Two database drivers out of the box** — MySQL (production default, matches `docker-compose.yml`) and SQLite (zero-config for demos / CI / local dev).
- **Completely redesigned UI** — modern typography (Inter + Playfair Display), coherent color palette, spacing scale, responsive grid, and a polished admin dashboard with a sticky sidebar that collapses on mobile.
- **Structured CSS architecture** — tokens → base → layout → components → pages. One `app.css` entry point imports the rest.
- **Solid security posture** —
  - Prepared statements everywhere (PDO, no string concatenation).
  - bcrypt password hashes (cost 12) via `password_hash` / `password_verify`.
  - CSRF tokens on every state-changing POST form; verified with `hash_equals`.
  - Destructive admin actions (delete user/post/category, toggle admin) use **POST + CSRF**, not bare `GET` links.
  - Output escaped with `htmlspecialchars(…, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')` via a short `e()` / `esc()` helper.
  - Uploads validated by extension and MIME type, filenames are randomised, destination directory is writability-checked, and PHP execution inside `public/uploads/` is blocked with `.htaccess`.
  - Secure session cookies (`HttpOnly`, `SameSite=Lax`, `Secure` when HTTPS is present). Session regenerates on login, clears on logout.
- **No composer or build step** — pure PHP 8.1+. CSS and JS are static files served directly.

## Project structure

```
blog/
├── app/
│   ├── Controllers/            # Request handlers (one class per resource)
│   │   ├── Admin/              # Admin-only controllers
│   │   ├── AuthController.php
│   │   ├── BlogController.php
│   │   ├── CategoryController.php
│   │   ├── ContactController.php
│   │   ├── HomeController.php
│   │   ├── PageController.php
│   │   ├── PostController.php
│   │   └── SearchController.php
│   ├── Core/                   # Framework-ish infrastructure
│   │   ├── Auth.php            # Session, login/logout, flash, old-input
│   │   ├── Database.php        # PDO wrapper (mysql + sqlite)
│   │   ├── Router.php          # Tiny front-controller router with groups
│   │   ├── View.php            # Template renderer with layouts & partials
│   │   └── helpers.php         # e(), url(), csrfField(), handleImageUpload(), …
│   └── Models/                 # All SQL lives here
│       ├── Category.php
│       ├── Post.php
│       └── User.php
│
├── resources/views/            # Presentation layer
│   ├── admin/                  # Admin pages (dashboard, posts, categories, users)
│   ├── auth/                   # Sign in / sign up
│   ├── blog/                   # Blog listing + search
│   ├── category/               # Category index
│   ├── errors/                 # 404
│   ├── layouts/                # site.php, admin.php
│   ├── pages/                  # About, Services, Contact
│   ├── partials/               # Navbar, sidebar, post card, flash …
│   ├── post/                   # Single post
│   └── home.php                # Homepage
│
├── public/                     # ← Apache document root
│   ├── assets/
│   │   ├── css/
│   │   │   ├── app.css         # Entry point — imports the layers below
│   │   │   ├── base/           # tokens, reset, typography, utilities
│   │   │   ├── components/     # buttons, forms, cards, tables, alerts, badges…
│   │   │   ├── layout/         # container, navbar, footer, dashboard
│   │   │   └── pages/          # home, blog, post, auth, info, admin
│   │   └── js/app.js           # Nav/sidebar toggle + alert auto-dismiss
│   ├── uploads/                # User uploads (thumbnails, avatars) — NO php execution
│   ├── .htaccess               # Rewrites every request to index.php
│   └── index.php               # Front controller
│
├── config/
│   └── app.php                 # Loads .env, defines constants
├── database/
│   ├── schema.mysql.sql        # MySQL DDL + seed data
│   ├── schema.sqlite.sql       # SQLite DDL + seed data
│   └── install.php             # One-shot install/seed script
├── routes/
│   └── web.php                 # All URL → controller mappings
├── storage/
│   └── logs/                   # Error log (production mode)
├── bootstrap.php               # Autoloader, error config, session, DB
├── router.php                  # Router for the PHP built-in server
├── Dockerfile                  # PHP 8.2 + Apache, DocumentRoot = /public
├── docker-compose.yml          # PHP + MySQL for local dev
├── .env.example                # Copy to .env and edit
└── .htaccess                   # Forwards root requests into /public (shared hosting)
```

## Running locally

### Option A — Docker (MySQL, mirrors production)

```bash
cp .env.example .env          # optional; defaults in docker-compose are fine
docker compose up --build
```

Then open <http://localhost:8080>. The first container start imports `database/schema.mysql.sql`, so the app has content the moment it boots.

Stop: `docker compose down` · wipe DB: `docker compose down -v`.

### Option B — PHP built-in server + SQLite (no install)

Zero dependencies beyond PHP 8.1+ with the `pdo_sqlite` extension.

```bash
# 1. Point the app at SQLite
cp .env.example .env
sed -i 's/DB_DRIVER=mysql/DB_DRIVER=sqlite/' .env

# 2. Create the database and seed it
php database/install.php

# 3. Run
php -S 127.0.0.1:8000 -t public router.php
```

Open <http://127.0.0.1:8000>.

### Option C — Classic LAMP (XAMPP / MAMP)

1. Drop the whole `blog/` folder under `htdocs/` (or your document root).
2. Create a MySQL database (e.g. `php_blog`) and import `database/schema.mysql.sql` via phpMyAdmin or the CLI.
3. Copy `.env.example` → `.env` and fill in your credentials.
4. Ensure `mod_rewrite` is enabled (it is by default on XAMPP). The root `.htaccess` transparently forwards every request into `/public`, so you can use `http://localhost/blog/`.
5. Make sure `public/uploads/` and `storage/logs/` are writable by your web server user.

## Seed accounts

| Role          | Username | Password      |
| ------------- | -------- | ------------- |
| Administrator | `admin`  | `Admin1234!`  |
| Member        | `johndoe`| `Password1!`  |

> ⚠ Change these immediately after the first login on any environment you care about.

## Design system

Defined in [`public/assets/css/base/_tokens.css`](public/assets/css/base/_tokens.css):

- **Colors** — `--color-primary` (indigo 600), `--color-accent` (orange 500), status colors (success / warning / danger / info), and a full neutral scale 0–900.
- **Typography** — Inter (body, UI) + Playfair Display (headings). Scale from `--fs-xs` 0.75rem up to `--fs-4xl` 3rem.
- **Spacing** — 4px scale: `--space-1` … `--space-20`.
- **Radius** — `--radius-xs` through `--radius-full`.
- **Elevation** — four-step shadow scale, plus a focus ring token.

Components live under `public/assets/css/components/`: buttons (`btn`, `btn--secondary`, `btn--ghost`, `btn--outline`, `btn--danger`, `btn--success`, sizes + icon variants), forms (`form-input`, `form-select`, `form-file`, `form-check`, `form-grid`), alerts, badges, avatars (5 sizes), cards, tables, empty-states.

## What was improved / fixed

| Area                | Before (original)                                       | Now                                                                                       |
| ------------------- | ------------------------------------------------------- | ----------------------------------------------------------------------------------------- |
| Architecture        | Flat PHP files mixing SQL, markup, and auth checks.     | MVC-like split: `app/Core`, `app/Controllers`, `app/Models`, `resources/views`, `routes/web.php`. |
| Front controller    | One PHP file per page, plus a mixed `admin/` folder.    | Single entry point at `public/index.php` with routed dispatch.                            |
| Database layer      | mysqli singleton, MySQL-only, typed `bind_param` strings. | PDO wrapper with the **same public API** (`fetchAll` / `fetchOne` / `execute` / `insert`); supports MySQL + SQLite. |
| URL scheme          | `admin/add-post.php`, `admin/store-post.php`, …         | Clean routes: `/admin/posts`, `/admin/posts/create`, `/admin/posts/store`.                |
| Destructive actions | GET links with JS `confirm()` (`?delete_id=5`).         | `POST` forms with CSRF tokens (`delete`, `toggle-admin`).                                 |
| Avatar uploads      | Silently rejected on some servers because `mime_content_type()` returned `application/octet-stream`; destination not writable-checked. | Validates by extension first, tolerates unknown MIME, pre-creates & verifies the destination directory, reports precise errors. |
| Form feedback       | Errors lost when redirecting; CSRF token consumed on re-edit failure. | Flash + old-input helpers; CSRF regenerated on success, preserved on validation errors. |
| CSS                 | One 740-line file with ad-hoc selectors + legacy classes. | Layered CSS architecture (base / layout / components / pages) with design tokens.        |
| Admin dashboard     | Cramped sidebar, inconsistent tables, no breadcrumbs.   | Sticky grouped sidebar (Main / Content / Site) that slides out on tablet/mobile; topbar with breadcrumbs and primary actions; redesigned tables with hover states and icon actions; stat cards. |
| Public site         | Generic cards, no featured hero, navbar fixed with overflow issues. | Hero section with featured post cutout, search bar, category chips, card grid with hover + zoom; beautiful single-post typography with 21:9 cinematic thumbnail. |
| Responsiveness      | Sidebar broke on narrow viewports; navbar overflow issues. | Fully responsive from 320px up; sidebar slides out, navbar collapses with a hamburger, forms and tables reflow gracefully. |
| Error pages         | None.                                                   | Styled 404 page.                                                                          |
| Uploads security    | `.htaccess` in `storage/uploads/`.                      | Uploads under `public/uploads/` with a stricter `.htaccess` + extension whitelist; MIME-sniffing blocked via `X-Content-Type-Options: nosniff`. |
| Headers             | None.                                                   | `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`, `X-Powered-By` removed.   |

## Assumptions

- PHP ≥ 8.1 with `pdo_mysql` (or `pdo_sqlite`), `gd`, `fileinfo`, and `mbstring` enabled. All standard on Docker Hub's `php:8.2-apache` and every recent LAMP stack.
- Apache with `mod_rewrite`. For Nginx, forward every non-file request to `/public/index.php` — a one-line `try_files $uri /index.php?$query_string;`.
- The app is served from either the project root (via the root `.htaccess` which forwards into `/public`) or `/public` directly. In both cases `ROOT_URL` is auto-detected.
- Uploaded images are stored locally in `public/uploads/`. On a multi-server deploy, mount this path on shared storage or swap it for S3 in `handleImageUpload()`.
- The ContactController stores a flash message on "send"; it does **not** actually deliver email. Hooking up `mail()`, PHPMailer, or an SMTP provider is a 5-line change in `app/Controllers/ContactController.php`.
- The "is_featured" flag is single-valued across the blog — featuring a post un-features whichever one was previously featured. This keeps the homepage hero predictable.
- No password-reset flow is shipped; admins can change a user's password from the admin panel. Adding self-service reset would just need a token table + mailer.

## License

MIT. Use it, ship it, rewrite it — just don't reupload the seed credentials to production.
#   n e o . b l o g  
 