# InterSys / InterBis — Evolución v2.0

> Sistemes de gestión ISP — Marzo 2026

---

## 📋 Resumen Ejecutivo

**Estado actual**: Sistema funcional que gestiona clientes, planes, puntos de acceso, cuotas mensuales y pagos. Usa Laravel 12 + Livewire 3 + Jetstream, Dockerizado. 

**Problemas críticos**: 
- Schema está en `db.sql`, no en migrations → no se puede hacer `migrate:fresh`
- **Cero tests de dominio** (15 tests totales, todos boilerplate de Jetstream)
- Lógica duplicada: generación de cuotas existe en 3 lugares diferentes
- Código de emails comentado y sin SMTP configurado
- **Sin autorización** (policies/gates): cualquier usuario autenticado puede hacer TODO

**Prioridad v2.0**: Habilitar notifications + emails transaccionales, fijar scheduling automático de cuotas, cerrar la brecha crítica de tests ≥50% coverage, añadir policies.

---

## 🎯 Objetivos de la v2.0

| Priority | Feature | Type | Story Points |
|----------|---------|------|--------------|
| **P0** | Sistema de Notifications (database + notifications table) | Feature + Arch | 5 |
| **P0** | Emails transaccionales habilitados + SMTP (quota generation, payment reminders) | Feature | 3 |
| **P0** | Fix: Generación automática de cuotas el día 1 — eliminar duplicación | Bug fix | 8 |
| **P1** | Migrations para domain tables (cliente, plan, pagos, cuotas, accespoint) | Tech Debt | 8 |
| **P1** | Tests unitarios: dominio (clients, contracts, quotas, payments) ≥50% coverage | Test | 13 |
| **P1** | Authorization layer: Policies + Gates para cada recurso | Security | 8 |
| **P2** | Email template library (Tailwind CSS components reusables) | Feature | 3 |
| **P2** | Factories para domain models (seeders de datos prueba) | DevTools | 5 |
| **P3** | API resources layer (JsonResource) desacoplado de Controllers | Arch | 5 |
| **P3** | Observers para dominio (Client, Payment, Quota — audit logging) | Feature | 3 |
| **P3** | Real-time notifications (Echo + Redis/Broadcasting) | Enhancement | 8 |

---

## 🔴 Problemas Críticos Detectados

### 1. **Schema en `db.sql` — NO en migrations**

```
❌ Actual: cliente, plan, pagos, cuotas, accespoint → solo en db.sql
✅ Objetivo: Migrations completas con foreign keys correctos
```

**Riesgo**: No puedes hacer `php artisan migrate:fresh` en staging/prod. Cada deploy depende de SQL manual.

**Impacto**: Bloquea testing pipeline futuro (Tinkerwell, Laravel Sail) y CI/CD conFresh Database`.

---

### 2. **Generación de cuotas duplicada en 3 lugares**

```
📁 app/Http/Controllers/quotaController.php → store() crea quota + pagos directly
📁 app/Console/Commands/GenerateMonthlyQuotas.php → hace lo mismo + email commented out  
📁 app/Jobs/GenerateQuotaPayments.php → queued job con chunked processing + cache progress
```

**Problema**: 
- Lógica duplicada → mantenibilidad nula
- `quotaController::store()` invoca el Job, que choca con lógica manual
- Scheduler (`routes/console.php`) apunta al Command vieja, no usa el Job

**Solución**: Consolidar TODO en un solo lugar (Service layer + dispatched Job). Desacoplar command de la lógica.

---

### 3. **Emails comentados — SMTP no configurado**

```php
// quotaController store() → commented out:
/*
Mail::to($client->email)->send(new QuotaGeneratedMail(...));
*/
```

**Problema**: 
- No hay `MAIL_HOST`, `MAIL_PORT`, `MAIL_ENCRYPTION` en `.env.example`
- `QuotaGeneratedMail.php` existe pero nunca se dispara
- Clientes no reciben notificaciones → cobranza manual

**Solución**: Configurar SMTP (Gmail/SMTP2GO/SendGrid), habilitar envío, añadir templates.

---

### 4. **Sin tests de dominio**

```
📁 tests/Feature → solo Jetstream boilerplate (auth, registration, 2FA)
📁 tests/Unit → un ExampleTest.php con assertTrue(true)
```

**Cobertura actual**: ~0% del dominio. 

**Solución**: Tests para controllers, services, jobs, policies. Objective: ≥50% coverage mínimo.

---

### 5. **Sin autorización — cualquier usuario puede borrar clientes/cuotas**

```php
// No hay Policies en app/Policies/
// No hay Gates en Auth::gate()
// Controllers solo chequean `auth()->check()` → cero granularidad
```

**Riesgo**: Si 2 admins (operador + supervisor) comparten el sistema, el operador puede borrar todo.

**Solución**: Roles (`admin`, `operator`, `auditor`) + Policies para cada modelo.

---

### 6. **Campo `email` en clients — NO existe en DB**

```
❌ Blade view: {{ $client->email }} → reference a column que no existe
❌ Migration cliente: no tiene email
✅ Fix: migrate client ADD COLUMN email VARCHAR(255) nullable unique
```

**Solución**: Añadimos email al dominio de Client (obligatorio para notifications).

---

### 7. **Tailwind dual v3 + v4 — CDN vs NPM**

```
📦 tailwindcss 3.4.0 → npm
📦 @tailwindcss/vite 4.0.0 → npm 
❌ <script src="cdn.tailwinds.com"> en app.blade.php
```

**Problema**: Inconsistencia de versiones, asset duplication, bundle size inflation.

**Solución**: Eliminar CDN, mantener solo v4 (NPM + Vite).

---

### 8. **Docker-compose → seed DB cada inicio**

```yaml
command: >
  php artisan key:generate --force &&
  php artisan config:clear &&
  php artisan cache:clear &&
  php artisan db:seed --force &&
  php-fpm
```

**Problema**: `db:seed` en cada restart puede crear datos duplicados o sobrescribir producción.

**Solución**: Seed solo si DB está vacía, o usar migration + seed scripts separados.

---

## 🚀 Feature Priorities — v2.0

### P0-1: Sistema de Notifications (Database)

**What**: Usar `Notifiable` trait en Client model, añadir notifications table via Laravel notification database driver.

**Why**: 
- Notificaciones in-app para eventos críticos (cuota generada, pago vencido, suspensión por deuda)
- Independiente de email → no requiere SMTP config

**How**:
```php
// 1. Add notifications migration
php artisan make:notification ClientQuotaGenerated

// 2. Store notification for client's user
$user->notify(new ClientQuotaGenerated($client, $quota));

// 3. Blade view para mostrar notifications (dropdown en navbar)
```

**Files**:
- `database/migrations/xxxx_create_notifications_table.php`
- `app/Notifications/ClientQuotaGenerated.php`, `PaymentOverdue.php`, `ClientSuspended.php`
- `resources/views/components/notification-bell.blade.php`
- `routes/web.php` → notification index route

**Story points**: 5

---

### P0-2: Emails Transaccionales Habilitados

**What**: Configurar SMTP + habilitar envíos para eventos críticos.

**Why**: Cobranza automática + reducción de soporte manual.

**How**:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=noreply@intersys.com
MAIL_PASSWORD=xxxxx
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@intersys.com
MAIL_FROM_NAME="${APP_NAME}"
```

**Events to notify**:
1. `QuotaGeneratedMail` — cuando se generan cuotas mensuales (ya existe)
2. `PaymentOverdueMail` — 3 días antes del vencimiento → reminder
3. `ClientSuspendedMail` — cliente dado de baja por >90 días deuda

**Files**:
- `.env.example` → añadir MAIL_* vars
- `app/Mail/PaymentOverdueMail.php`, `ClientSuspendedMail.php` (nuevos)
- `resources/views/emails/payment-overdue.blade.php`, etc.
- Update `routes/console.php` scheduler → disparar reminders

**Story points**: 3

---

### P0-3: Fix — Generación Automática de Cuotas (Día 1 del mes)

**Current state**:
```php
// routes/console.php
Schedule::command('quotas:Generate')->monthlyOn(1, '00:05')

// app/Console/Commands/GenerateMonthlyQuotas.php → logic duplicada
// app/Jobs/GenerateQuotaPayments.php → queued job con progress tracking (unused)
```

**Goal**: Consolidar en un solo service layer + dispatched Job.

**Implementation**:
```php
// 1. New Service: app/Services/QuotaCreationService.php
public function createMonthlyQuota(string $monthYear): array 
{
  // Business logic: check duplicate, create quota record, dispatch payments job
}

// 2. Update command → call service instead of inline logic
php artisan make:command CreateMonthlyQuotaCommand

// 3. Scheduler → new command name
Schedule::command('quotas:create-monthly')->monthlyOn(1, '05:00')
```

**Fixes**:
- Eliminamos `quotaController::store()` duplicación (solo UI + service call)
- Job `GenerateQuotaPayments` ahora se dispara desde Service (chunked progress tracking)
- Scheduler apunta al service → command solo wire

**Files**:
- `app/Services/QuotaCreationService.php` (new)
- Update `app/Console/Commands/CreateMonthlyQuotaCommand.php`
- Update `routes/console.php` scheduler
- Delete `GenerateMonthlyQuotas.php` old logic

**Story points**: 8

---

### P1-1: Migrations para Domain Tables

**Current**: 5 migrations (users, cache, jobs) en `database/migrations/`, core domain ONLY in `db.sql`.

**Goal**: Migraciones completas con foreign keys correctos.

**Migration list**:
```php
CreatePlanTable.php → plan (id, nombre, megabytes [int], costo [decimal])
CreateAccessPointTable.php → accespoint (id, ssid, frecuencia, ip_ap nullable, localidad)
CreateClientTable.php → cliente (id, id_plan FK→plan, id_point FK→accespoint, ...) + add email column
CreateQuotaTable.php → cuotas (id, numero)
CreatePaymentTable.php → pagos (id, id_cliente FK→cliente, id_cuota FK→cuotas, ...)
```

**Foreign keys**: 
- `cliente.id_plan` ON DELETE RESTRICT (plan no se delete si tiene clients)
- `cliente.id_point` ON DELETE CASCADE (accespoint puede delete con clients linked)
- Add indexes en foreign columns para query performance

**Files**: 5 migration files in `database/migrations/`

**Story points**: 8

---

### P1-2: Tests Unitarios de Dominio ≥50% Coverage

**Current**: 1 test unitario (ExampleTest) + 14 feature tests (todos Jetstream). **Cobertura dominio: 0%.**

**Goal**: Tests para cada controller service job → cobertura ≥50%.

**Test plan**:
```php
// Clients feature tests
test('can create a client with valid data');
test('cannot create client without contract');
test('cannot create client with duplicate IP');
test('can ban and unban clients');
test('banned clients cannot be reactivated without manual approval')

// Contracts
test('cannot delete contract that has clients associated');
test('can update plan megabytes and price')

// Quotas
test('quota creation sends notification to admin');
test('quota generation dispatches payment job for all clients');
test('cannot generate duplicate quota for same month-year')

// Payments
test('payment record cannot be edited once status is paid');
test('can upload voucher images for payment proof');

// Unit tests (services)
QuotaCreationServiceTest → createMonthlyQuota() logic
```

**Files**: 
- `tests/Feature/ClientTest.php` (new)
- `tests/Feature/ContractTest.php` (new)
- `tests/Feature/QuotaTest.php` (new)
- `tests/Unit/Services/QuotaCreationServiceTest.php` (new)
- `.github/workflows/tests.yml` — CI pipeline para run tests

**Story points**: 13

---

### P1-3: Authorization Layer — Policies + Gates

**Current**: Cero autorización. Any authenticated user → full access.

**Goal**: Roles + Policies para cada recurso (Client, Contract, Quota, Payment).

**Implementation**:
```php
// AppServiceProvider boot() 
Gate::define('delete-client', function ($user, $client) {
    return $user->can_delete_clients; // role check
});

// app/Policies/ClientPolicy.php
public function update(User $user){Client} $client): bool
{
    return in_array($user->role, ['admin', 'operator']);
}

// Controllers → authorize() method
protected function authorizeUpdate($id)
{
    $client = Client::findOrFail($id);
    Gate::authorize('update', $client);
}
```

**Roles**: 
- `admin` — full access to everything
- `operator` — CRUD clients, contracts, quotas (NO delete)
- `auditor` — read-only view all reports

**Files**:
- Add `role` column in users table migration 
- `app/Policies ClientPolicy.php`, ContractPolicy.php`, QuotaPolicy.php`, PaymentPolicy.php`
- Update controllers → `authorize()` calls en cada action

**Story points**: 8

---

### P2-1: Email Template Library (Tailwind CSS Components Reusables)

**Current**: `resources/views/emails/quota-generated.blade.php` existe pero hardcoded.

**Goal**: Librería modular de email templates con Tailwind CSS compatible (SparkPost, Mailgun ready).

**Template list**:
```blade
components/emails/header.blade.php
components/emails/footer.blade.php  
components/emails/button.blade.php
components/emails/table.blade.php

email/quota-generated.html → usa components above
emails/payment-overdue.html
emails/client-suspended.html
emails/welcome.html
```

**Tailwind CSS for emails**: Compatible con Mailchimp, SendGrid, Mailgun (inline styles).

**Story points**: 3

---

### P2-2: Factories para Domain Models

**Current**: Solo `UserFactory` en `database/factories/`. Cero factories domain.

**Goal**: Factories para todos los models → seeders de prueba realistas.

```php
Client::factory()->define(function (array $attributes) {
    return [
        'nombre' => Faker::name(),
        'apellido' => Faker::lastName(),
        'email' => Faker::unique()->safeEmail,
        // ... all fields
    ];
});
```

**Factories needed**: ClientFactory, ContractFactory, AccessPointFactory, QuotaFactory, PaymentFactory

**Use cases**: Tests seeding, `php artisan tinker` demo data.

**Story points**: 5

---

### P3-1: API Resources Layer (JsonResource)

**Current**: Controllers return Models directly in `$request->user()`, no desacoplamiento.

**Goal**: JsonResource wrappers para responses consistentes + transformación de datos.

```php
class ClientResource extends JsonResource {
    public function toArray($request) {
        return [
            'id' => $this->id,
            'name' => "{$this->nombre} {$this->apellido}",
            'email' => $this->email,
            'contract_name' => $this->contract()->nombre, // relationship eager load
            // ...
        ];
    }
}
```

**Benefits**: 
- desacopa models de API responses
- easy to version (v1, v2) endpoints
- future GraphQL/REST compatibility

**Story points**: 5

---

## 🏗️ Arquitectura — Mejoras Estructurales

### Service Layer Introducción

**Problem**: Controllers tienen lógica de negocio (quota creation, payment processing). No reusable.

**Solution**: Services layer desacoplado → controllers solo orquestan.

```
app/Services/
├── ClientService.php → client validations + business rules
├── ContractService.php → plan logic
├── QuotaCreationService.php → monthly quota creation (single source of truth)
└── PaymentService.php → payment validation + processing
```

**Migrations**: Move all controller business logic to services. Inject via constructor.

---

### Observers para Audit Logging

**Goal**: Registrar cambios críticos en auditoria table (who, when, what changed).

```php
class ClientObserver {
    public function updated(Client $client) {
        AuditLog::create([
            'model_type' => Client::class,
            'model_id' => $client->id,
            'action' => 'updated',
            'old_values' => $client->getOriginal(),
            'new_values' => $client->getAttributes(),
            'user_id' => auth()->id()
        ]);
    }
}
```

**Observers**: ClientObserver, PaymentObserver, QuotaObserver (en `app/Observers/`)

---

### Real-time Notifications con Echo + Redis

**Goal**: Live updates sin refresh page (cuotas generadas, pagos registrados, nuevos clientes).

**Backend**: Laravel Echo + Pusher o Redis broadcasting

```php
// In QuotaCreated event:
event(new QuotaCreated($quota));
broadcast(new NotificationEvent(...))->toOthers();
```

**Frontend**: Vue.js/Echo integration in app.blade.php — `echo.channel('notifications')`

---

### CI/CD Pipeline (GitHub Actions)

**Goal**: Auto-run tests en cada push/PR, linting with Laravel Pint.

```yaml
name: Tests
on: [push, pull_request]
jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - run: composer install --no-interaction
      - run: php artisan migrate:fresh --seed
      - run: ./vendor/bin/phpunit --coverage=text
```

**Linting**: Laravel Pint para PHP formatting consistent.

---

## 📚 Roadmap — v2.0 Timeline

### Phase 1 — Critical Fixes (Weeks 1-2)

| Day | Task |
|-----|------|
| 1-2 | Migrations domain tables + test `migrate:fresh` |
| 3-4 | Fix quota generation consolidation (Service layer) |
| 5 | Add email column to clients |
| 6-7 | SMTP config + enable email notifications |
| Weekend | Review + documentation |

**Deliverables**: Working migrations, no duplicate quota logic, emails funcionando.

---

### Phase 2 — Test Coverage (Weeks 3-4)

| Day | Task |
|-----|------|
| 1-3 | Client tests (create, update, ban/unban, export PDFs) |
| 4-5 | Contract + AccessPoint tests |
| 6-7 | Quota + Payment service tests |
| Weekend | Coverage report, fix failures |

**Deliverables**: ≥50% test coverage, GitHub Actions CI pipeline running.

---

### Phase 3 — Security + Polish (Weeks 5-6)

| Day | Task |
|-----|------|
| 1-3 | Policies + Gates implementation |
| 4-5 | Notifications system (database + UI dropdown) |
| 6-7 | Role column in users table + seed sample roles |

**Deliverables**: Authorization layer completo, role-based access control.

---

## 🧾 Checklist v2.0 — Acceptance Criteria

### P0 — Critical Path ✅

- [ ] Migrations domain tables completas
- [ ] `php artisan migrate:fresh --seed` funciona end-to-end
- [ ] Cuotas se generan automáticamente día 1 del mes (cron verificable)
- [ ] Emails enviados correctamente → test con SMTP sandbox
- [ ] Notifications database table exists + UI dropdown visible

### P1 — Foundation ✅

- [ ] Test coverage ≥50% en dominio
- [ ] Factories para todos domain models
- [ ] Policies implementadas para cada recurso crítico
- [ ] GitHub Actions CI pipeline green

### P2 — Enhancement ✅

- [ ] Email templates reusables (header, footer, button components)
- [ ] Audit logging via observers (client updates, payment changes)

### P3 — Future Work ✅

- [ ] API resources layer para desacoplamiento
- [ ] Real-time notifications con Echo + Redis

---

## 🔍 Technical Debt Register

| ID | Description | Status | Priority | Notes |
|----|-------------|--------|----------|-------|
| TD-01 | Client email column missing from db.sql | Open | P0 | Added to migration list |
| TD-02 | Empty userController methods | Open | P3 | Can be deleted or stubbed |
| TD-03 | Payment controller index/create/store/destroy empty | Open | P2 | Implement or remove routes |
| TD-04 | Dual Tailwind v3 + v4 | Open | P3 | Migrate to v4 only |
| TD-05 | `db:seed` in docker-compose auto-start | Open | P1 | Change to conditional seed |
| TD-06 | No foreign key indexes | Open | P1 | Add indexes on FK columns |

---

## 📝 Notes

**Environment Variables**: `.env.example` debe reflejar producción. Añadir:

```env
# Mail configuration
MAIL_MAILER=smtp
MAIL_HOST=${SMTP_HOST}
MAIL_PORT=587
MAIL_USERNAME=${SMTP_USERNAME}
MAIL_PASSWORD=${SMTP_PASSWORD}
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@intersys.com
MAIL_FROM_NAME="${APP_NAME}"

# Redis for queue + broadcasting
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Queue driver
QUEUE_CONNECTION=database
```

**Backup Strategy**: Antes de migrar: backup de `db.sql` → restore manual script preservado. Migrations nuevas se aplican con test en staging primero.

**Documentation Needed**: 
- Post-migration guide (how to use migrations vs old SQL approach)
- Email setup docs (SMTP providers tested + config)  
- Quota generation debug commands (`php artisan app:generate-monthly-quotas`)

---

## 🎬 Next Steps

1. **Create issue tracker** para cada P0/P1 task con acceptance criteria
2. **Setup staging environment** con fresh migration pipeline
3. **Backup production data** antes de cambios estructurales
4. **Enable GitHub Actions CI** con test suite (verificar green)
5. **Schedule deployment** para v2.0 — target: 6 semanas

---

**Document version**: 1.0  
**Last updated**: August 2026  
**Author**: Architect Team + User Requirements (notifications, emails, quota scheduling)
