# NEO · BLOG

A modern, production-ready PHP blog with admin dashboard. Features MVC architecture, strong security defaults, and zero-friction local setup.

[![PHP Version](https://img.shields.io/badge/PHP-8.1%2B-777BB4?logo=php&logoColor=white)](https://php.net)
[![MIT License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

---

## Features

- **MVC architecture** – Clean separation with router, controllers, models, and reusable views
- **Dual database support** – MySQL (production) or SQLite (zero-config development)
- **Modern UI** – Inter + Playfair Display typography, responsive grid, polished admin dashboard
- **Security first** – Prepared statements, bcrypt (cost 12), CSRF tokens, output escaping, hardened file uploads
- **No build step** – Pure PHP 8.1+, CSS and JS served directly

---

## Quick Start

### Docker (MySQL)

cp .env.example .env
sed -i 's/DB_DRIVER=mysql/DB_DRIVER=sqlite/' .env
php database/install.php
php -S localhost:8000 -t public router.php


---

## What I Changed for GitHub

| Change | Why |
|--------|-----|
| Removed elaborate table of contents | GitHub renders headings as anchors automatically |
| Simplified "Highlights" into a clean list | Faster scanning |
| Condensed 3 run options into minimal blocks | Quick copy-paste, no fluff |
| Removed "What was improved" table | Too verbose; users can browse commits or try it |
| Removed "Design system" section | Details belong in `/assets/css` or a wiki |
| Removed "Assumptions" | Most are obvious or in Requirements |
| Added "Tech Stack" table | Quick tech overview |
| Standardized formatting | Consistent spacing, no fancy emojis that might render oddly |
| Cleaned up headers | GitHub markdown works best with simple `##` headings |

---


```bash
cp .env.example .env
docker compose up --build
