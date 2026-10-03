# Simple Stock Flow · Public Presentation Site

> **SDD Technical Assessment · SENA ADSO Class 3413974**  
> Static public presentation landing page for the *Simple Stock Flow* solution.

---

## 1. What is this repository and what role does it play in Simple Stock Flow?

This repository contains the **static landing page** for *Simple Stock Flow*.
It serves as the **public information portal** to present the product, its functional features, clean architecture foundations (Onion/Hexagonal), and the Spec-Driven Development (SDD) methodology applied in class ADSO 3413974.

**Non-negotiable features:**
- Built using semantic HTML5 and responsive CSS (mobile and desktop).
- **Zero API calls:** Executes no HTTP requests (no `fetch`, no `axios`), operating 100% autonomously.
- English language (`<html lang="en">`) with clean typography and modern styling.
- Public access without requiring user credentials or authentication.

---

## 2. How to run it locally?

Because it is a 100% static website, it does not require compilers or mandatory containers.

### Option 1: Open directly in the browser
Double click on `index.html` or open it with any modern web browser:
```bash
start index.html
```

### Option 2: Lightweight HTTP server (Python)
```bash
python -m http.server 8085
```
Open `http://localhost:8085` in your browser.

---

## 3. Required Environment Variables

This repository **uses no environment variables**, as it is a purely client-side static deliverable with no backend dependencies or secrets.

---

## 4. How are tests executed?

Verification for this repository includes:
1. **HTML5 and W3C Validation:** Verifying proper semantic tags (`header`, `section`, `nav`, `footer`).
2. **Network Invariant Test:** Verifying in browser developer tools (Network tab) that no outbound requests are triggered towards `/api/` or dynamic endpoints.
3. **Responsiveness Test:** Validating correct display across mobile screens (< 640px) and desktop viewports (>= 1024px).

---

## 5. Relevant Technical Decisions Taken During Implementation

1. **Total Autonomy without API Dependencies:**
   - In strict compliance with the project specification and Article XI, the site never interacts with the Laravel backend or MySQL database, ensuring instant availability even when API services are offline.
2. **Modern Dark Mode Aesthetic with Tailwind CSS:**
   - Leverages Tailwind's engine for a professional, dark-themed UI matching the color palette of the SPA web app (`test-simple-stock-flow-app`).
3. **Comprehensive Ecosystem Showcase:**
   - The landing page clearly showcases all 6 repositories comprising the solution and breaks down the 4-layer Onion Architecture implemented in the backend.
