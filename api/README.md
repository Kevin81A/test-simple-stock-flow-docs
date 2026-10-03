# Simple Stock Flow · Backend REST API (PHP / Laravel)

> **Spec-Driven Development (SDD) Technical Benchmark · Class 3413974**  
> Transactional backend implementation under Strict Clean Architecture (Onion / Hexagonal) with PHP 8.2 and Laravel 11.

---

## 1. What is this repository and what role does it play in Simple Stock Flow?

This repository contains the **transactional REST API** governing business logic, MySQL persistence, and role-based access control for *Simple Stock Flow*.

Key responsibilities:
- **Authentication & RBAC:** Stateless JWT tokens (symmetric HS256 with 30s clock leeway) and role control (`admin` and `seller`). Enforces DP-04 by restricting admin account creation.
- **Product Catalog Management:** Product CRUD, soft delete, pagination, keyword search, category filtering, and image upload with MIME validation (JPEG/PNG/WebP up to 5 MB) stored in `/var/www/media`.
- **Atomic Point of Sale:** Concurrent transactions protected by optimistic locking (`version` column with up to 3 automatic retries), atomic stock deductions, and unique product lines.
- **Consolidated Reporting:** High-efficiency SQL queries preserving historical frozen product names (DP-01) and dynamic total calculations.
- **Schema Ownership (ADR-001):** Manages database migrations with 25 columns, 21 constraints, 13 indices, 9 engine checks, and 5 fixed seed categories.

---

## 2. How to run the project locally?

### With Docker Compose (Recommended)
From `test-simple-stock-flow-infra` or root monorepo:

```bash
docker compose up -d --build api
```
The API will be available at `http://localhost:8000`.

### Without Docker (Local PHP 8.2+ Environment)
Requires PHP 8.2+ with `pdo_mysql`, `mbstring`, `openssl`, and running MySQL instance:

```bash
# 1. Install Composer dependencies
composer install

# 2. Configure environment
cp .env.example .env
# Set DB credentials and JWT_SIGNING_KEY in .env

# 3. Run database migrations
php artisan migrate --force

# 4. Bootstrap initial admin account
php artisan app:bootstrap-admin

# 5. Start local server
php artisan serve --port=8000
```

---

## 3. Required Environment Variables

| Variable | Description | Default / Example Value |
|---|---|---|
| `APP_ENV` | Environment mode | `production` or `local` |
| `APP_DEBUG` | Display stack traces | `false` |
| `APP_URL` | Base API URL | `http://localhost:8000` |
| `DB_CONNECTION` | Database engine | `mysql` |
| `DB_HOST` | Database host | `db` (Docker) or `127.0.0.1` |
| `DB_PORT` | MySQL connection port | `3306` |
| `DB_DATABASE` | Database name | `stockflow` |
| `DB_USERNAME` | Database username | `stockflow` |
| `DB_PASSWORD` | Database password | `stockflowpass` |
| `JWT_SIGNING_KEY` | Symmetric secret key | Required in production (min 32 chars) |
| `ADMIN_EMAIL` | Initial admin email | `admin@stockflow.com` |
| `ADMIN_PASSWORD` | Initial admin password | Required in production (min 8 chars) |
| `MEDIA_ROOT` | Storage directory for images | `/var/www/media` |

---

## 4. How are tests executed?

```bash
# Automated tests via PHPUnit
php artisan test
```

---

## 5. Key Technical Decisions Made During Implementation

1. **Strict Onion Architecture (Article I of Constitution):**
   - **`Domain/` (Pure PHP):** Zero dependencies on Laravel, Eloquent, or external libraries. Houses entities (`Product`, `Sale`, `SaleItem`, `User`, `Category`), Value Objects (`Money`, `Quantity`, `DateRange`), exceptions, and repository interfaces.
   - **`Application/`:** Atomic use cases (1 action = 1 class) and decoupled DTOs.
   - **`Infrastructure/`:** Eloquent models segregated from domain entities via explicit bidirectional mappers.
   - **`Presentation/`:** HTTP controllers, Form Requests returning `400 application/problem+json`, and JWT middleware.
2. **Invariant D-C9 Compliance (Empty Error Bodies):**
   - 401, 403, 404, and 405 error responses return with an empty body (`Content-Length: 0`). 400 and 422 errors return RFC 7807 `application/problem+json`.
3. **Optimistic Locking Concurrency Handling (BR-11 / HTTP 409):**
   - `RegisterSaleUseCase` incorporates automatic retries for concurrent sales.
4. **Sales Report with Frozen Names (PD-01):**
   - Partitioned subqueries fetch the most recent frozen name of each product in the queried period.