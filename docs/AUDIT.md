# ISP Management — Security Audit & Improvement Plan

**Date:** October 30, 2026  
**Status:** Production system — audit findings and phased improvement plan

---

## Progress Tracker

| Phase | Status | Date |
|-------|--------|------|
| Phase 1 — Security | ✅ DONE | 2026-10-30 (`f5f8d8c`) |
| Phase 2 — Architecture | ✅ DONE | 2026-10-30 (`cae320e`) |
| Medium fixes | ✅ DONE | 2026-10-30 (`fdf38b4`) |
| Phase 3 — Product Maturity | ✅ DONE | 2026-10-30 (`97d673c`) |

---

---

## 🔴 Critical — Immediate Action Required

### C1. Auto-registration → Admin by default

**Location:** `migrations/2026_08_17_000003_add_role_to_users_table.php`, `config/fortify.php`  
**Evidence:**

```php
// Migration: every new user gets admin role
$table->enum('role', ['admin', 'operator', 'auditor'])
      ->default('admin');

// Fortify: open registration enabled
Features::registration();
```

**Impact:** Anyone who registers becomes a full admin with access to the entire system. This is a complete system takeover vector.

**Fix:** Disable `Features::registration()` or change default role to `'operator'` and re-enable registration with email verification.

---

### C2. Test user credentials hardcoded in seeder

**Location:** `database/seeders/DatabaseSeeder.php`  
**Evidence:**

```php
User::firstOrCreate(
    ['email' => 'test@example.com'],
    ['name' => 'Test User', 'password' => bcrypt('password')],
);
```

**Impact:** Running `db:seed` in production recreates a known user with password `"password"`.

**Fix:** Wrap in `if (app()->environment('production'))` guard or remove entirely.

---

### C3. Admin notifications sent to all users

**Location:** `app/Console/Commands/RemindOverduePayments.php`, `app/Jobs/GenerateQuotaPayments.php`  
**Evidence:**

```php
// RemindOverduePayments.php — sends notifications to EVERY user
$admins = User::all();  // ← Not only admins

// GenerateQuotaPayments.php — picks first user, whoever it is
$adminUser = User::first();
```

**Impact:** All users (auditors, operators, external users) receive sensitive financial notifications about overdue payments.

**Fix:** Filter by `$admins = User::where('role', 'admin')->get();`.

---

### C4. Email verification disabled

**Location:** `config/fortify.php`  
**Evidence:**

```php
// Features::emailVerification(),  ← Commented out
```

**Impact:** Combined with open registration and default admin role, an attacker registers from any email and gains admin immediately.

**Fix:** Enable `Features::emailVerification()` and disable self-registration, or require admin-invite-only registration.

---

### C5. Nginx missing security headers

**Location:** `docker/nginx/default.conf`  
**Evidence:** No security headers configured.

**Impact:** Vulnerable to clickjacking, XSS, MIME sniffing, and MITM without HSTS.

**Fix:** Add `X-Frame-Options`, `X-Content-Type-Options`, `X-XSS-Protection`, `Content-Security-Policy`, `Strict-Transport-Security`, `Referrer-Policy` headers.

---

### C6. Default database credentials in Docker Compose

**Location:** `docker-compose.yml`  
**Evidence:**

```yaml
MYSQL_ROOT_PASSWORD: ${DB_ROOT_PASSWORD:-rootpassword}
MYSQL_PASSWORD:     ${DB_PASSWORD:-admin123456}
```

**Impact:** If `.env` is missing or misconfigured, network-internal attackers can connect with predictable credentials.

**Fix:** Remove fallback defaults — require credentials via `.env` only.

---

## 🟠 High — Security & Architecture

### H1. Payment `estado` in `$fillable` — Mass assignment vulnerability

**Location:** `app/Models/Payments.php`  
**Evidence:**

```php
protected $fillable = [
    'id_cliente', 'costo', 'abonado', 'estado',  // ← estado is mass-assignable
];
```

**Impact:** An authenticated user could mark any payment as paid (`estado=1`) by including it in a request, manipulating financial records.

**Fix:** Remove `estado` from `$fillable`. Mark payments as paid explicitly via `update(['estado' => 1])`.

---

### H2. QuotaController has no authorization checks

**Location:** `app/Http/Controllers/quotaController.php`  
**Evidence:** No `$this->authorize()` call in `store()`.

**Impact:** Any authenticated user (including auditors who should only read) can generate monthly quotas, potentially duplicating invoices or sabotaging billing cycles.

**Fix:** Add `$this->authorize('create', Quota::class)` to `store()` and wire `QuotaPolicy` in AuthServiceProvider.

---

### H3. `toggleBan()` in Livewire without authorization

**Location:** `app/Livewire/ClientsTable.php`  
**Evidence:**

```php
public function toggleBan($id)
{
    $client = Client::findOrFail($id);
    $client->update(['is_banned' => !$client->is_banned]);
    // No authorize check — any authenticated user can ban/unban
}
```

**Impact:** Any authenticated user can ban/unban clients. An auditor could use this for sabotage.

**Fix:** Add `$this->authorize('ban', $client)` before the update.

---

### H4. Quota unique constraint blocks future years

**Location:** `migrations/2024_01_01_000004_create_cuotas_table.php`, `migrations/2026_08_17_000002_add_unique_to_cuotas_numero.php`  
**Evidence:**

```php
// Only stores month number (1-12), unique across ALL time
$table->integer('numero')->nullable();
$table->unique('numero');  // ← Quota numero=2 exists from 2026, cannot create for 2027
```

**Impact:** Once a quota `numero=2` exists, you cannot generate a February quota for any other year. The billing system is **broken for future years**.

**Fix:** Add a `year` column to `cuotas`, change unique to `(numero, year)`.

---

### H5. Predictable file names for uploaded images

**Location:** `app/Http/Controllers/clientController.php`  
**Evidence:**

```php
$nombreImagen = time() . '.' . $request->file_upload->extension();
```

**Impact:** Predictable sequential filenames allow authenticated users to browse all client images via URL guessing.

**Fix:** Use `Str::uuid()` or `Str::random(32)` instead of `time()`.

---

### H6. Soft-delete cascade inconsistency

**Location:** `app/Models/Client.php`, `migrations/2024_01_01_000005_create_pagos_table.php`  
**Evidence:** `Client` uses `SoftDeletes` but payments don't handle soft-deleted clients gracefully.

**Impact:** Payments reference soft-deleted clients, causing inconsistent statistics and orphaned records.

**Fix:** Add `->withTrashed()` to related queries or use `onDelete('set null')` on foreign keys.

---

## 🟡 Medium — Quality & Performance

### M1. N+1 queries in Statistics view

**Location:** `app/Livewire/Statistics.php`  
**Evidence:** ~10 separate queries per render, no eager loading or caching.

**Fix:** Consolidate with `selectRaw`, `withCount`, and cache the result.

---

### M2. Dashboard cache key typo

**Location:** `app/Livewire/Dashboard.php`  
**Evidence:** `cache()->remember('stast_global', 30, ...)` — typo "stast" instead of "stats".

**Fix:** Correct cache key and verify all usages reference the correct key.

---

### M3. Sanctum tokens never expire

**Location:** `config/sanctum.php`  
**Evidence:** `'expiration' => null`

**Fix:** Set a reasonable expiration (e.g., 30 days).

---

### M4. Role checks as strings without validation

**Location:** All Policies (`app/Policies/`)  
**Evidence:** Raw string comparisons `'admin'`, `'operator'`, `'auditor'` — no enum, no integrity validation.

**Fix:** Add a DB-level check constraint or use the User model constants for role checking.

---

### M5. No audit trail

**Impact:** No record of who modified payments, contracts, or clients.

**Fix:** Implement `owen-it/laravel-auditing` or custom Observers.

---

### M6. Incomplete test coverage for new features

**Evidence:** No tests for quota creation, statistics, ban/unban, PDF export, Livewire components, or scheduled commands.

**Fix:** Write feature tests for critical flows: quota generation → payment registration → payment update.

---

### M7. No dedicated scheduler container

**Location:** `docker-compose.yml`  
**Evidence:** `routes/console.php` defines scheduled tasks but Docker has no container running `schedule:run`.

**Fix:** Add a `scheduler` service to docker-compose.

---

### M8. Missing database indexes on frequently queried columns

**Evidence:** No indexes on `pagos.created_at`, `pagos.estado`, `cliente.id_plan`.

**Fix:** Add composite indexes on columns used in joins and filters.

---

## 📋 Phased Improvement Plan

### Phase 1 — Security (Immediate)

| # | Action | Effort |
|---|--------|--------|
| 1.1 | Disable `Features::registration()` or change default role | Low |
| 1.2 | Enable `Features::emailVerification()` | Low |
| 1.3 | Remove `estado` from Payments `$fillable` | Low |
| 1.4 | Add authorization to QuotaController and toggleBan() | Low |
| 1.5 | Remove test user from production seeder | Very low |
| 1.6 | Add security headers to Nginx | Low |
| 1.7 | Filter admin notifications by role | Low |
| 1.8 | Set Sanctum token expiration | Very low |

### Phase 2 — Architecture (Medium priority)

| # | Action | Effort |
|---|--------|--------|
| 2.1 | Fix quota `numero` unique constraint — add `year` column | Medium |
| 2.2 | Add audit trail (Observers or package) | High |
| 2.3 | Optimize Statistics queries with eager loading + cache | Medium |
| 2.4 | Add scheduler container to Docker | Medium |
| 2.5 | Use UUIDs for uploaded file names | Low |
| 2.6 | Validate actual MIME type of uploaded files | Low |
| 2.7 | Add database indexes on frequently queried columns | Low |

### Phase 3 — Product maturity (Optional but valuable)

| # | Action | Effort |
|---|--------|--------|
| 3.1 | Real-time dashboard with Livewire polling | Medium |
| 3.2 | Email notifications to clients (monthly invoice) | High |
| 3.3 | Financial report exports (PDF, CSV) | High |
| 3.4 | Granular roles with Spatie Laravel-Permission | High |
| 3.5 | Advanced search with debounce | Medium |
| 3.6 | Health checks + monitoring | Medium |
| 3.7 | End-to-end tests for payment flow | High |

---

## Environment Details

| Item | Value |
|------|-------|
| Framework | Laravel 12.x |
| PHP | 8.3 |
| Database | MySQL 8.0 |
| Frontend | Livewire 3.6 / Jetstream / Tailwind |
| Auth | Fortify + Sanctum + 2FA |
| Deployment | Docker Compose + Nginx + PHP-FPM |
| Queue | MySQL (database driver) |
