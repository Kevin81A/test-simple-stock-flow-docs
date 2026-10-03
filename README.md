# Simple Stock Flow · Unified Monorepo

> **Spec-Driven Development (SDD) Technical Benchmark**  
> **National Learning Service (SENA) · Software Analysis and Development (ADSO) · Class 3413974**  
> **Developer:** Kevin ([`Kevin81A`](https://github.com/Kevin81A))  
> **Production Stack:** PHP 8.2 (Laravel 11) + React 18 (TypeScript + Vite) + MySQL 8.4 LTS + Docker Compose  

---

## 📌 Master Technical Documentation
For the complete technical report featuring Mermaid diagrams (Onion Architecture, ER Model, Sequence Flow) and requirement traceability, see:  
👉 **[TECHNICAL-DELIVERY.md](TECHNICAL-DELIVERY.md)**

---

## 1. What is this repository and what role does it play in Simple Stock Flow?

This repository is the **Unified Monorepo** consolidating all 6 components of *Simple Stock Flow* into a single, cohesive, and directly executable codebase:

```text
test-simple-stock-flow/
├── docker-compose.yml          # Root orchestration (1 command to start all services)
├── docker-compose.dev.yml      # Local development port mapping
├── .env.example                # Pre-configured environment variables
├── verify.sh / verify.ps1      # Automated test suites for probes P-01 through P-42
├── TECHNICAL-DELIVERY.md       # Master technical architecture & compliance report
│
├── api/                        # REST Backend (Laravel 11 / PHP 8.2) - Pure Onion Architecture
├── app/                        # Frontend SPA (React 18 + TypeScript + Vite) served via Nginx
├── infra/                      # Infrastructure assets and environment definitions
├── docs/                       # Original SDD specifications (Python / .NET), ADRs & constitution
├── page/                       # Responsive static public landing page (zero API calls)
└── tool/                       # Python CLI seeder tool for idempotent demo data population
```

---

## 2. How to run the project locally? (Quickstart)

### With Docker Compose (Recommended - Single Command)
The root `docker-compose.yml` builds and orchestrates the full ecosystem out of the box:

```bash
# 1. Clone the monorepo
git clone https://github.com/Kevin81A/test-simple-stock-flow.git
cd test-simple-stock-flow

# 2. Start services in the background
docker compose up -d --build

# 3. Check container health status
docker compose ps
```

#### Application Endpoints
- **Web Application (React SPA):** [http://localhost:8080](http://localhost:8080)
- **REST API (Laravel 11):** [http://localhost:8000](http://localhost:8000)
- **Healthcheck Probe:** [http://localhost:8000/health](http://localhost:8000/health)
- **Static Landing Page:** Open `page/index.html` in your browser.

#### Initial Credentials
- **Administrator Role:** Username `admin@stockflow.com` (or `admin`) / Password `Admin12345!`
- **Seller Role:** Registered via `/vendedores/nuevo` by an authenticated administrator (enforcing DP-04).

---

## 3. Required Environment Variables

The `.env.example` file in the root provides default configurations:

| Variable | Description | Default Value |
|---|---|---|
| `DB_ROOT_PASSWORD` | MySQL root database password | `rootsecret` |
| `DB_DATABASE` | Database name | `stockflow` |
| `DB_USERNAME` | Application database user | `stockflow` |
| `DB_PASSWORD` | Application database password | `stockflowpass` |
| `JWT_SIGNING_KEY` | Symmetric HS256 secret key | `super_secret_jwt_key_stock_flow_2026_adso_3413974` |
| `ADMIN_EMAIL` | Initial administrator email | `admin@stockflow.com` |
| `ADMIN_PASSWORD` | Initial administrator password | `Admin12345!` |

*Security Notice (Article IX): In production environments, secrets must not use default values.*

---

## 4. How are tests and automated verification executed?

The repository includes cross-platform automated test suites testing probes P-01 through P-42:

### On Linux / macOS / Git Bash:
```bash
./verify.sh
```

### On Windows (PowerShell):
```powershell
.\verify.ps1
```

### Backend Automated Unit Tests (PHPUnit):
```bash
docker compose exec api php artisan test
```

### Seeding Demo Data (Optional):
```bash
cd tool
docker build -t ssf-tool .
docker run --rm --network host ssf-tool seed
```

---

## 5. Key Technical Decisions Made During Implementation

1. **Unified Monorepo for Evaluation Ergonomics:**
   - Allows evaluators to clone one repository and launch the whole solution with `docker compose up -d --build`, eliminating sibling folder path friction.
2. **Strict Onion Architecture in Backend (Article I):**
   - Pure PHP 8.2 domain layer, completely decoupled from Eloquent and Laravel.
3. **Optimistic Locking with Versioning (BR-11 / HTTP 409):**
   - Concurrency resolution using version checks and up to 3 automatic retries during sale registration.
4. **RFC 7807 & Invariant D-C9 Error Compliance:**
   - 401, 403, 404, and 405 responses emit an empty body (`Content-Length: 0`). 400 and 422 return `application/problem+json`.
5. **Decoupled Static Presentation Site (`page/`):**
   - Independent HTML5 landing page with zero network coupling to the API.

---

## 6. Sibling Repositories & Forks

This monorepo unifies the modular repositories previously synchronized:
* Central Monorepo: [https://github.com/Kevin81A/test-simple-stock-flow](https://github.com/Kevin81A/test-simple-stock-flow)
* Backend API: [https://github.com/Kevin81A/test-simple-stock-flow-api](https://github.com/Kevin81A/test-simple-stock-flow-api)
* Frontend SPA: [https://github.com/Kevin81A/test-simple-stock-flow-app](https://github.com/Kevin81A/test-simple-stock-flow-app)
* Infrastructure: [https://github.com/Kevin81A/test-simple-stock-flow-infra](https://github.com/Kevin81A/test-simple-stock-flow-infra)
* Documentation: [https://github.com/Kevin81A/test-simple-stock-flow-docs](https://github.com/Kevin81A/test-simple-stock-flow-docs)
* Static Site: [https://github.com/Kevin81A/test-simple-stock-flow-page](https://github.com/Kevin81A/test-simple-stock-flow-page)
* CLI Tool: [https://github.com/Kevin81A/test-simple-stock-flow-tool](https://github.com/Kevin81A/test-simple-stock-flow-tool)