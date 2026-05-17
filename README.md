# ordermo

A lightweight PHP MVC web app for an on-demand delivery service covering cities
in Zambales, Bataan, Bulacan, and Pampanga. No framework — a custom router,
controllers, models, and PDO.

## Requirements

- PHP 8.0+ (PHP 8.3 tested)
- MySQL / MariaDB (XAMPP's MariaDB on port `3306`)
- XAMPP (for the Apache option)

## 1. Database setup

The schema and seed data live in [`database.sql`](database.sql). It creates the
`ordermo` database plus the `cities`, `users`, `customers`, `merchants`,
`riders`, and `admins` tables.

Start MySQL, then import:

**Windows (XAMPP, from CMD):**

```cmd
C:\xampp\mysql\bin\mysql.exe -u root -p < "C:\xampp\htdocs\ordermo\database.sql"
```

**WSL / Linux:**

```bash
mysql -u root -p < database.sql
```

Re-running the script is safe — it drops and recreates the tables, so use it to
re-sync the schema after changes.

Verify:

```sql
SHOW DATABASES;       -- expect: ordermo
USE ordermo;
SHOW TABLES;
```

## 2. Configuration

Database credentials are in [`app/config/config.php`](app/config/config.php):

```php
define('DB_HOST', 'localhost');
define('DB_PORT', 3306);     // XAMPP MariaDB default
define('DB_NAME', 'ordermo');
define('DB_USER', 'root');
define('DB_PASS', '...');    // set this to your MySQL root password
```

Set `DB_PASS` to your MySQL root password. On a fresh XAMPP install the root
password is empty (`''`).

`BASE_URL` auto-detects how the app is served — no need to change it.

## 3. Running the app

Pick **one** of the two options below.

### Option A — XAMPP / Apache

1. Place the project at `C:\xampp\htdocs\ordermo`.
2. In the XAMPP Control Panel, start **Apache** and **MySQL**.
3. Open:

   **http://localhost/ordermo/public/**

> Must include the trailing `/public/`. Opening `http://localhost/ordermo/`
> will break asset and link paths. Apache uses
> [`public/.htaccess`](public/.htaccess) for URL rewriting (`mod_rewrite` is on
> by default in XAMPP).

### Option B — PHP built-in server (no XAMPP needed)

From the project root:

```bash
php -S localhost:8000 router.php
```

Then open:

**http://localhost:8000**

[`router.php`](router.php) serves static assets from `public/` with correct MIME
types and routes everything else to the app, so it works regardless of the
directory the server is started from (no `-t public` flag needed). MySQL still
needs to be running for pages to load.

## Routes

| URL | Description |
|---|---|
| `/` | Landing page (featured cities) |
| `/auth/login` | Customer log in / sign up |
| `/auth/register` | Customer registration |
| `/auth/logout` | Log out |
| `/merchant/login` | Merchant log in |
| `/merchant/apply` | Apply as a merchant |
| `/rider/login` | Rider log in |
| `/rider/apply` | Apply as a rider |
| `/admin/login` | Admin log in |

(Prefix with `http://localhost:8000` for Option B, or
`http://localhost/ordermo/public` for Option A.)

## Project structure

```
app/
  config/      App + database configuration
  controllers/ Request handlers
  core/        App router, base Controller, PDO Database
  models/      City, User
  views/       Templates (layouts/, home/, auth/, merchant/, rider/, admin/)
public/        Web root — index.php, css/, js/, images/, .htaccess
router.php     Router for the PHP built-in server
database.sql   Schema + seed data
```

## Troubleshooting

- **Page is unstyled (raw HTML):** hard-refresh with `Ctrl+Shift+R` to clear the
  cached page. With the PHP built-in server, use the command exactly as shown
  above.
- **Database connection error:** MySQL isn't running, `ordermo` wasn't imported,
  or `DB_PASS` in [`app/config/config.php`](app/config/config.php) doesn't match
  your MySQL root password.
- **Links/CSS break under Apache:** make sure the URL includes `/public/`.
