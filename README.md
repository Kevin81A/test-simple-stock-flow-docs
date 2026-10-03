# Simple Stock Flow · Especificación y Documentación Maestra (SDD)

> **Prueba Técnica de Desempeño SDD · Ficha ADSO 3413974**  
> Repositorio central de documentación, especificación original y entrega técnica del proyecto *Simple Stock Flow*.

---

## 📌 Documentación de Entrega Técnica del Reto
Para consultar el informe completo de arquitectura, diagramas Mermaid (Onion, ER, Secuencia), matriz de trazabilidad de reglas de negocio y guía de evaluación, consulte el documento maestro:
👉 **[ENTREGA-TECNICA.md](ENTREGA-TECNICA.md)**

---

## 1. ¿Qué es este repositorio y qué rol cumple en Simple Stock Flow?

Este repositorio es la **fuente de verdad canónica** y el centro de documentación del ecosistema *Simple Stock Flow*.
Cumple el rol de:
- Guardar la especificación original en dos versiones conceptuales: `spec-python/` y `spec-.net/`.
- Establecer la **Constitución de 13 Artículos Innegociables** para el desarrollo con enfoque SDD (*Spec-Driven Development*).
- Consignar las **Decisiones de Arquitectura (ADRs)** y los contratos OpenAPI / RFC 7807 que rigen la solución.
- Servir de guía metodológica para demostrar cómo una especificación abstracta se materializa en una arquitectura limpia de producción en **PHP (Laravel 11)** y **React 18**.

---

## 2. ¿Cómo se ejecuta y navega la solución?

Los seis repositorios se clonan como **directorios hermanos** en una carpeta contenedora común:

```bash
# Estructura requerida en el host:
workspace/
├── test-simple-stock-flow-api/     # Backend en Laravel (puerto 8000)
├── test-simple-stock-flow-app/     # Frontend React SPA en Nginx (puerto 8080)
├── test-simple-stock-flow-docs/    # Documentación y especificación
├── test-simple-stock-flow-infra/   # Docker Compose y base de datos MySQL 8.4
├── test-simple-stock-flow-page/    # Sitio público estático de presentación
└── test-simple-stock-flow-tool/    # Herramienta CLI de sembrado en Python
```

### Ejecución de los servicios
Desde la carpeta `test-simple-stock-flow-infra`:
```bash
docker compose up -d --build
```
- **Aplicación Web (SPA):** `http://localhost:8080`
- **API REST:** `http://localhost:8000`
- **Sonda de Salud:** `http://localhost:8000/health`

---

## 3. Variables de entorno requeridas

Este repositorio de documentación no ejecuta procesos ni requiere variables de entorno en tiempo de ejecución.
Las variables de entorno de la infraestructura completa están centralizadas en `test-simple-stock-flow-infra/.env.example` y documentadas en detalle en [`ENTREGA-TECNICA.md`](ENTREGA-TECNICA.md).

---

## 4. ¿Cómo se ejecutan las pruebas y validación del sistema?

En el repositorio de infraestructura (`test-simple-stock-flow-infra`) se encuentran los scripts de pruebas integrales para comprobar las sondas (P-01 a P-42):
- En Linux / macOS / Git Bash: `./verify.sh`
- En Windows PowerShell: `.\verify.ps1`

En el backend (`test-simple-stock-flow-api`):
- `php artisan test`

---

## 5. Decisiones técnicas relevantes tomadas durante la implementación

1. **Traducción Rigurosa del Spec a Laravel + React:**
   - La especificación original utilizaba Python/.NET como ejemplos de especificación agnóstica. Se realizó una traducción 1 a 1 de todas las entidades, reglas y contratos hacia **PHP 8.2 puro en Dominio**, **Laravel 11 en Infraestructura/Presentación** y **React 18 + Vite en el Frontend**.
2. **Arquitectura Onion de 4 Capas (Artículo I):**
   - El núcleo de dominio no tiene ninguna referencia a Laravel ni a Eloquent. Los modelos de base de datos (`ProductModel`, `SaleModel`, etc.) se encuentran estrictamente confinados en `app/Infrastructure/Persistence/Models` y se comunican con el dominio a través de Mappers bidireccionales.
3. **Persistencia y Bloqueo Optimista (RN-11 / 409):**
   - Para evitar condiciones de carrera en ventas simultáneas, se implementó control de versiones con hasta 3 reintentos automáticos en el caso de uso `RegisterSaleUseCase`.
4. **Respuestas de Error Conformantes (Invariante D-C9):**
   - Respuestas 401, 403, 404 y 405 retornan cuerpo estrictamente vacío (`Content-Length: 0`). Respuestas 400 y 422 retornan objetos normalizados bajo RFC 7807 (`application/problem+json`).

---

## 6. Los Seis Repositorios del Ecosistema en GitHub

| Repositorio | Descripción | Enlace en GitHub |
|---|---|---|
| `test-simple-stock-flow-docs` | Especificación original, constitución y entrega técnica | [Kevin81A/test-simple-stock-flow-docs](https://github.com/Kevin81A/test-simple-stock-flow-docs) |
| `test-simple-stock-flow-api` | Backend REST en PHP 8.2 + Laravel 11 (Arquitectura Onion) | [Kevin81A/test-simple-stock-flow-api](https://github.com/Kevin81A/test-simple-stock-flow-api) |
| `test-simple-stock-flow-app` | Frontend SPA en React 18 + Vite + TypeScript (Nginx) | [Kevin81A/test-simple-stock-flow-app](https://github.com/Kevin81A/test-simple-stock-flow-app) |
| `test-simple-stock-flow-infra` | Orquestación Docker Compose, MySQL 8.4 y verificación | [Kevin81A/test-simple-stock-flow-infra](https://github.com/Kevin81A/test-simple-stock-flow-infra) |
| `test-simple-stock-flow-page` | Sitio público estático de presentación (cero API) | [Kevin81A/test-simple-stock-flow-page](https://github.com/Kevin81A/test-simple-stock-flow-page) |
| `test-simple-stock-flow-tool` | Herramienta CLI de sembrado idempotente en Python | [Kevin81A/test-simple-stock-flow-tool](https://github.com/Kevin81A/test-simple-stock-flow-tool) |
