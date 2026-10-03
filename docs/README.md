# Simple Stock Flow · Master Specification & Documentation (SDD)

> **SDD Technical Assessment · SENA ADSO Class 3413974**  
> Central repository for master documentation, original specifications, and technical deliverables for the *Simple Stock Flow* project.

---

## 📌 Master Technical Delivery Report
For the complete architectural report, Mermaid diagrams (Onion layers, ER diagram, sequence flows), business rules traceability matrix, and evaluation guide, see the master document:  
👉 **[TECHNICAL-DELIVERY.md](TECHNICAL-DELIVERY.md)** (also available in Spanish as **[ENTREGA-TECNICA.md](ENTREGA-TECNICA.md)**)

---

## 1. What is this repository and what role does it play in Simple Stock Flow?

This repository is the **canonical source of truth** and documentation hub for the entire *Simple Stock Flow* ecosystem.
Its responsibilities include:
- Preserving the original dual specifications: `spec-python/` and `spec-.net/`.
- Defining the **13 Non-Negotiable Constitutional Articles** for Spec-Driven Development (SDD).
- Documenting all **Architectural Decision Records (ADRs)** and OpenAPI / RFC 7807 contracts governing the solution.
- Providing a methodological reference demonstrating how an abstract specification is implemented as a clean production architecture in **PHP 8.2 (Laravel 11)** and **React 18**.

---

## 2. How to run and navigate the solution?

All six repositories are cloned as **sibling directories** inside a shared workspace folder:

```bash
# Required host structure:
workspace/
├── test-simple-stock-flow-api/     # Laravel REST Backend (port 8000)
├── test-simple-stock-flow-app/     # React 18 SPA on Nginx (port 8080)
├── test-simple-stock-flow-docs/    # Documentation & Specifications
├── test-simple-stock-flow-infra/   # Docker Compose & MySQL 8.4 Database
├── test-simple-stock-flow-page/    # Static Public Presentation Site
└── test-simple-stock-flow-tool/    # Python Idempotent Seeder CLI
```

### Running the Services
From the `test-simple-stock-flow-infra` folder:
```bash
docker compose up -d --build
```
- **Web Application (SPA):** `http://localhost:8080`
- **REST API:** `http://localhost:8000`
- **Healthcheck:** `http://localhost:8000/health`

---

## 3. Required Environment Variables

This documentation repository executes no runtime processes and requires no environment variables.
All environment configuration for the complete infrastructure is centralized in `test-simple-stock-flow-infra/.env.example` and fully documented in [`TECHNICAL-DELIVERY.md`](TECHNICAL-DELIVERY.md).

---

## 4. How are tests and system verification executed?

The infrastructure repository (`test-simple-stock-flow-infra`) contains comprehensive automated verification scripts covering probes P-01 through P-42:
- On Linux / macOS / Git Bash: `./verify.sh`
- On Windows PowerShell: `.\verify.ps1`

In the backend (`test-simple-stock-flow-api`):
- `php artisan test`

---

## 5. Relevant Technical Decisions Taken During Implementation

1. **Rigorous Spec Translation to Laravel + React:**
   - The original specification utilized Python/.NET as agnostic reference examples. A 1:1 translation was performed for all entities, rules, and contracts into **pure PHP 8.2 in Domain**, **Laravel 11 in Infrastructure/Presentation**, and **React 18 + Vite in Frontend**.
2. **4-Layer Onion Architecture (Article I):**
   - The domain core has zero references to Laravel or Eloquent. Database models (`ProductModel`, `SaleModel`, etc.) are strictly confined to `app/Infrastructure/Persistence/Models` and interact with the domain exclusively through bidirectional mappers.
3. **Persistence and Optimistic Concurrency Locking (RN-11 / 409):**
   - To prevent race conditions in concurrent sales, version-based optimistic locking with up to 3 automatic retries was implemented in `RegisterSaleUseCase`.
4. **Strict Conforming Error Responses (Invariant D-C9):**
   - HTTP 401, 403, 404, and 405 return strictly empty bodies (`Content-Length: 0`). HTTP 400 and 422 return RFC 7807 compliant payloads (`application/problem+json`).

---

## 6. The Six Ecosystem Repositories on GitHub

| Repository | Description | GitHub Link |
|---|---|---|
| `test-simple-stock-flow-docs` | Original specification, constitution, and technical delivery | [Kevin81A/test-simple-stock-flow-docs](https://github.com/Kevin81A/test-simple-stock-flow-docs) |
| `test-simple-stock-flow-api` | REST Backend in PHP 8.2 + Laravel 11 (Onion Architecture) | [Kevin81A/test-simple-stock-flow-api](https://github.com/Kevin81A/test-simple-stock-flow-api) |
| `test-simple-stock-flow-app` | Frontend SPA in React 18 + Vite + TypeScript (Nginx) | [Kevin81A/test-simple-stock-flow-app](https://github.com/Kevin81A/test-simple-stock-flow-app) |
| `test-simple-stock-flow-infra` | Docker Compose orchestration, MySQL 8.4, and verification | [Kevin81A/test-simple-stock-flow-infra](https://github.com/Kevin81A/test-simple-stock-flow-infra) |
| `test-simple-stock-flow-page` | Static presentation site (zero API calls) | [Kevin81A/test-simple-stock-flow-page](https://github.com/Kevin81A/test-simple-stock-flow-page) |
| `test-simple-stock-flow-tool` | Python idempotent CLI demo seeder | [Kevin81A/test-simple-stock-flow-tool](https://github.com/Kevin81A/test-simple-stock-flow-tool) |
