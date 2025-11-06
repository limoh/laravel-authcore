# AuthCore

**AuthCore** is a lightweight, modular authentication and authorization core library for modern web applications.  
It provides secure token-based authentication (JWT), role-based access control (RBAC), session management, and integration with external identity providers like OAuth2 and OpenID Connect.

---

## 🚀 Key Features

- 🔐 **JWT Token Management** – Issue, verify, and refresh tokens securely.  
- 👥 **Role & Permission Control** – Fine-grained access management (RBAC).  
- 🌐 **OAuth2 / OpenID Connect Support** – Integrate with Google, Azure AD, etc.  
- 🧩 **Modular Architecture** – Designed for microservices or standalone auth servers.  
- 🧠 **Introspection & Revocation** – Token verification and session invalidation endpoints.  
- 🪵 **Audit Logging** – Track logins, refreshes, and security events.  

---

## 🧭 Authentication Flow

```mermaid
flowchart TD
  A[Client (Browser / Mobile)] -->|Login credentials| B[AuthCore API / Auth Service]
  B -->|Validate credentials| C[User Store (DB / LDAP)]
  C -->|Success| D[Issue JWT Access Token + Refresh Token]
  D -->|Return tokens| A
  A -->|Request (Authorization: Bearer <token>)| E[Protected API]
  E -->|Verify token (signature & claims)| B
  B -->|Token valid| E
  E -->|Access granted| F[Resource]
  E -->|Token expired| G[Refresh Endpoint]
  A -->|Send refresh token| G
  G -->|Validate refresh token| B
  G -->|Issue new access token| A
