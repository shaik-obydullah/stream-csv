# StreamCSV

StreamCSV is a small Slim 4 application for previewing and exporting large MySQL tables to CSV. It streams rows using keyset pagination (`ORDER BY primary key`) to avoid `OFFSET` slowdowns on large datasets.

## Features
- Slim 4 micro-framework structure (`public/index.php`, actions, services)
- Orders preview page with escaped output
- CSV export endpoint that streams a downloadable file (`/export`)
- Keyset/batch-based export (configurable batch size)
- Environment-based configuration
- Docker Compose setup (PHP + Apache + MySQL) with sample schema/seed

## Requirements
- PHP 8.1+ with `pdo_mysql`
- Composer
- MySQL/MariaDB

## Quick start (Docker)
1. Build and start containers:
   ```bash
   docker compose up --build
   ```
2. Install PHP dependencies inside the app container (first run):
   ```bash
   docker compose exec app composer install
   ```
3. Open [http://localhost:8080](http://localhost:8080)
   - Preview: `/`
   - Download CSV: `/export`

The database container initializes with `database/init.sql` (schema + small sample rows).

## Local setup (without Docker)
1. Install dependencies:
   ```bash
   composer install
   ```
2. Copy environment config and adjust DB settings:
   ```bash
   cp .env.example .env
   ```
   (Load `.env` into your environment or export the variables in your shell/server config.)
3. Create/import the database schema:
   - Small sample: `database/init.sql`
   - Large legacy dataset (original project dump): `db_csvbigly.sql`
4. Serve the app from `public/` (example):
   ```bash
   php -S localhost:8080 -t public
   ```

## Configuration
| Variable | Default | Description |
| --- | --- | --- |
| `APP_DEBUG` | `false` | Show detailed error messages |
| `DB_HOST` | `127.0.0.1` | Database host |
| `DB_PORT` | `3306` | Database port |
| `DB_DATABASE` | `db_csvbigly` | Database name |
| `DB_USERNAME` | `root` | Database user |
| `DB_PASSWORD` | *(empty)* | Database password |
| `DB_CHARSET` | `utf8mb4` | Connection charset |
| `EXPORT_TABLE` | `orders` | Table to export |
| `EXPORT_PRIMARY_KEY` | `order_id` | Primary key used for keyset pagination |
| `EXPORT_BATCH_SIZE` | `1000` | Rows fetched per batch |
| `PREVIEW_LIMIT` | `10` | Rows shown on the preview page |

## Project structure
```text
public/            # Front controller and web root
src/
  Action/          # HTTP actions (preview, export)
  Database/        # PDO connection factory
  Export/          # Streaming CSV exporter
templates/         # View templates
config/            # App settings
database/          # Schema/sample init SQL
docker/            # Container build files
```

## Notes
- Exports are streamed as a download response; no merged report files are written to the app directory.
- The exporter writes the CSV header once and emits rows in primary-key order.
- `db_csvbigly.sql` is retained from the original project as a large sample dataset for manual import/testing.
