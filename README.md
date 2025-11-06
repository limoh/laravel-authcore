# Laravel-AuthCore

**Laravel-AuthCore** is a lightweight, modular authentication and authorization micro-library built on Laravel.  
It provides secure token-based authentication (JWT), role-based access control (RBAC), session/refresh token management, and support for identity provider integration — enabling a flexible auth layer for Laravel-based applications and microservices.

---

## ✅ Key Features

- Issue and verify JSON Web Tokens (JWTs) and handle refresh tokens.  
- Role & permission management (RBAC) for fine-grained access control.  
- Built for Laravel applications: easy integration into middleware, service providers, controllers.  
- Extensible architecture — you can swap out token services, user stores, or identity providers.  
- Includes audit logging hooks (login, logout, token refresh, token revoke).  
- Ideal for microservices, API-first apps, and modular Laravel apps.

---

## 🧭 Authentication Flow

```mermaid
flowchart TD
  A[Client (Browser / Mobile App)] -->|Login credentials| B[AuthCore (Laravel API)]
  B -->|Validate credentials| C[User Store (DB / Eloquent)]
  C -->|Success| D[Issue JWT Access Token + Refresh Token]
  D -->|Return tokens to client| A
  A -->|Calls API: Authorization: Bearer <access_token>| E[Protected API Endpoint]
  E -->|Middleware verifies token| B
  B -->|Token valid| E
  E -->|Access granted| F[Resource]
  E -->|Token expired| G[Refresh Endpoint]
  A -->|Send refresh token| G
  G -->|Validate refresh token| B
  G -->|Issue new access token (± new refresh token)| A

## 📁 Folder Structure

laravel-authcore/
├─ app/                    # Laravel “app” directory (Controllers, Models, Services)
├─ bootstrap/              # Laravel bootstrap files
├─ config/                 # Configuration files (e.g., authcore.php, jwt.php settings)
├─ database/               # Migrations, seeders for auth tables (users, roles, permissions, tokens)
├─ public/                 # Public web root, index.php, assets
├─ resources/              # Blade templates, language files (if UI included)
├─ routes/                 # Web/API route definitions (e.g., routes/api.php)
├─ storage/                # Laravel storage (logs, cache, session, etc)
├─ tests/                  # Unit and feature tests for AuthCore functionality
├─ .env.example            # Environment variable template
├─ .gitignore              # Git ignore patterns
├─ composer.json           # PHP dependencies and autoload config
├─ package.json            # (If front-end assets) JS dependencies
├─ phpunit.xml             # PHPUnit configuration
├─ vite.config.js          # If using Vite for front-end build
└─ README.md               # This file
