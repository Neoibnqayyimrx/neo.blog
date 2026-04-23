# NEO · BLOG

Project Description

NEO · BLOG is a modern, production-ready PHP blogging platform engineered with a clean MVC-inspired architecture, robust security practices, and a fully redesigned user experience. The project transforms a traditional procedural PHP application into a scalable, maintainable system suitable for real-world deployment.

At its core, the application separates concerns across controllers, models, and views, supported by a lightweight custom routing system and a flexible database layer powered by PDO. It supports both MySQL (for production environments) and SQLite (for quick local setup and testing), making it highly portable and developer-friendly.

The platform features a complete blogging ecosystem, including post management, category organization, user authentication, and an intuitive admin dashboard. The admin interface has been carefully redesigned with a modern UI/UX approach, featuring responsive layouts, a collapsible sidebar, structured navigation, and improved data presentation through clean tables and actionable components.

A strong emphasis was placed on security and reliability. The system implements prepared statements throughout, bcrypt password hashing, CSRF protection for all state-changing actions, secure session handling, and hardened file upload mechanisms. Additional safeguards such as output escaping, strict HTTP headers, and restricted upload execution ensure the application adheres to modern web security standards.

On the frontend, the project introduces a structured CSS architecture built on design tokens, enabling consistency in typography, spacing, colors, and components. The result is a visually cohesive interface that adapts seamlessly across devices, from mobile to desktop.

Beyond functionality, the project is designed for ease of deployment and onboarding. It can be run instantly using Docker for a production-like environment, or via a lightweight PHP server with SQLite for zero-configuration setup. This dual approach makes it ideal for both demonstration and real-world usage.

Overall, NEO · BLOG demonstrates a full-stack understanding of PHP application design—combining backend architecture, frontend design systems, and security best practices into a single, cohesive product.

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
