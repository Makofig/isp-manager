# InterSys / InterBis — Evolución v2.0

> Sistema de gestión ISP — Agosto 2026

---

## 📋 Resumen Ejecutivo

**Estado actual**: Sistema funcional que gestiona clientes, planes, puntos de acceso, cuotas mensuales y pagos. Usa Laravel 12 + Livewire 3 + Jetstream, Dockerizado.

**Problemas críticos**:
- Schema está en `db.sql`, no en migrations → no se puede hacer `migrate:fresh`
- **Cero tests de dominio** (15 tests totales, todos boilerplate de Jetstream)
- Lógica duplicada: generación de cuotas existe en 3 lugares diferentes
- Código de emails comentado y sin SMTP configurado
- **Sin autorización** (policies/gates): cualquier usuario autenticado puede hacer TODO
- SweetAlert2 vía CDN → mezcla de tecnologías, sin componentes reusables

**Prioridad v2.0**: UI components propia con Alpine.js, habilitar notifications + emails transaccionales, fix scheduling cuotas, cerrar brecha tests ≥50%, policies.

---

## 🎯 Objetivos de la v2.0

| Priority | Feature | Type | Story Points |
|----------|---------|------|--------------|
| **P0** | Sistema de Notifications (database + UI) | Feature + Arch | 5 |
| **P0** | UI Components propia (Alpine.js, reemplaza SweetAlert2) | Arch | 8 |
| **P0** | Emails transaccionales habilitados + SMTP | Feature | 3 |
| **P0** | Fix: Generación automática de cuotas el día 1 — eliminar duplicación | Bug fix | 8 |
| **P1** | Migrations para domain tables | Tech Debt | 8 |
| **P1** | Tests unitarios: dominio ≥50% coverage | Test | 13 |
| **P1** | Authorization layer: Policies + Gates | Security | 8 |
| **P2** | Email template library (Tailwind CSS components) | Feature | 3 |
| **P2** | Factories para domain models | DevTools | 5 |
| **P3** | API resources layer (JsonResource) | Arch | 5 |
| **P3** | Observers para dominio (audit logging) | Feature | 3 |
| **P3** | Real-time notifications (Echo + Redis) | Enhancement | 8 |

---

## 🔴 Problemas Críticos Detectados

### 1. **Schema en `db.sql` — NO en migrations**
```
❌ Actual: cliente, plan, pagos, cuotas, accespoint → solo en db.sql
✅ Objetivo: Migrations completas con foreign keys correctos
```

### 2. **Generación de cuotas duplicada en 3 lugares**
```
quotaController::store() → quota + pagos directly
GenerateMonthlyQuotas.php → misma lógica + email commented out
GenerateQuotaPayments.php → queued job (unused)
```

### 3. **SweetAlert2 vía CDN — sin componentes reusables**
```
❌ Actual: CDN script + Swal.fire() duplicado en 16 lugares
✅ Objetivo: UI components propia con Alpine.js, cero dependencias externas
```

### 4. **Emails comentados — SMTP no configurado**

### 5. **Sin tests de dominio**

### 6. **Sin autorización**

### 7. **Campo `email` en clients — NO existe en DB**

### 8. **Tailwind dual v3 + v4 — CDN vs NPM**

### 9. **Docker-compose → seed DB cada inicio**

---

## 🚀 Feature Priorities — v2.0

### P0-1: Sistema de Notifications (Database + UI)

**What**: Notificaciones in-app con `Notifiable` trait + UI dropdown en navbar.

**Scope**:
1. Migration `notifications` table (standard Laravel)
2. 3 notification classes: `QuotaGenerated`, `PaymentOverdue`, `ClientSuspended`
3. `NotificationsBell` Livewire component en navbar (izquierda del user dropdown)
4. Routes: `/notifications`, mark-read, mark-all-read
5. ≥3 feature tests

**Implementation steps**:
```bash
# 1. Create migration (Laravel includes it, just run)
php artisan notifications:table
php artisan migrate

# 2. Create notification classes
php artisan make:notification QuotaGenerated
php artisan make:notification PaymentOverdue
php artisan make:notification ClientSuspended

# 3. Create Livewire component
php artisan make:livewire NotificationsBell

# 4. Add routes to routes/web.php
Route::get('/notifications', NotificationIndex::class)->name('notifications.index');
Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead']);
Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead']);

# 5. Insert bell in navigation-menu.blade.php (before user dropdown)
```

**Story points**: 5

---

### P0-2: UI Components Propia (Alpine.js) — Reemplaza SweetAlert2

**What**: Librería de componentes UI construida con Alpine.js + Tailwind CSS. Reemplaza SweetAlert2 (CDN) con componentes nativos reusables.

**Why**:
- SweetAlert2 es una dependencia externa pesada (~50KB)
- Alpine.js ya viene con Livewire → zero overhead
- Componentes consistentes con el design system de Tailwind
- Sin CDN → funciona offline, versionado en repo
- Mejor accesibilidad y control del DOM

**Components to build**:

| Component | Purpose | Alpine directives |
|-----------|---------|-------------------|
| `x-confirm` | Confirm dialog (replaces `Swal.fire` delete confirm) | `x-data`, `x-show`, `x-transition` |
| `x-toast` | Toast notifications (success, error, warning, info) | `x-data`, `x-init`, `setTimeout` |
| `x-modal` | Generic modal (replaces Jetstream modals) | `x-show`, `@keydown.escape` |
| `x-dropdown` | Enhanced dropdown (notifications, menus) | `x-data`, `@click.outside` |
| `x-loading` | Loading spinner/overlay | `x-show`, `x-transition` |
| `x-empty-state` | Empty state placeholder | Static + slot |
| `x-badge` | Status badges (active, banned, paid, overdue) | Static + variant prop |
| `x-datatable` | Reusable data table wrapper | `x-data`, search, sort, pagination |

**Implementation steps**:

#### Step 1: Remove SweetAlert2
```blade
<!-- resources/views/layouts/app.blade.php — DELETE -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
```

#### Step 2: Create Blade components directory
```
resources/views/components/ui/
├── confirm.blade.php
├── toast.blade.php
├── modal.blade.php
├── dropdown.blade.php
├── loading.blade.php
├── empty-state.blade.php
├── badge.blade.php
└── datatable.blade.php
```

#### Step 3: Create `x-confirm` component
```blade
{{-- resources/views/components/ui/confirm.blade.php --}}
@props([
    'title' => '¿Estás seguro?',
    'message' => 'No podrás revertir esta acción.',
    'confirmText' => 'Sí, confirmar',
    'cancelText' => 'Cancelar',
    'confirmAction' => null,  // Livewire action or JS callback
    'confirmId' => null,      // ID to pass to action
])

<div x-data="{
    open: false,
    title: @js($title),
    message: @js($message),
    confirmText: @js($confirmText),
    cancelText: @js($cancelText),
    action: @js($confirmAction),
    id: @js($confirmId),
    loading: false,
    confirm() {
        this.loading = true;
        if (this.action) {
            @this.call(this.action, this.id).then(() => {
                this.open = false;
                this.loading = false;
            });
        }
    }
}" @keydown.escape.window="open = false">
    
    {{-- Trigger slot --}}
    <div @click="open = true">
        {{ $trigger }}
    </div>

    {{-- Dialog --}}
    <template x-teleport="body">
        <div x-show="open" 
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
             style="display: none;">
            
            <div @click.outside="open = false"
                 x-transition:enter="ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                
                <h3 class="text-lg font-semibold text-gray-900" x-text="title"></h3>
                <p class="mt-2 text-sm text-gray-600" x-text="message"></p>
                
                <div class="mt-6 flex justify-end gap-3">
                    <button @click="open = false" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        <span x-text="cancelText"></span>
                    </button>
                    <button @click="confirm" 
                            :disabled="loading"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 disabled:opacity-50">
                        <span x-show="!loading" x-text="confirmText"></span>
                        <span x-show="loading">Procesando...</span>
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>
```

#### Step 4: Create `x-toast` component
```blade
{{-- resources/views/components/ui/toast.blade.php --}}
@props(['position' => 'top-right'])

<div x-data="{
    toasts: [],
    add(type, message) {
        const id = Date.now();
        this.toasts.push({ id, type, message });
        setTimeout(() => this.remove(id), 4000);
    },
    remove(id) {
        this.toasts = this.toasts.filter(t => t.id !== id);
    }
}" 
x-on:toast.window="add($event.detail.type, $event.detail.message)"
@toast.window="add($event.detail.type, $event.detail.message)"
class="fixed z-50 space-y-2 {{ $position === 'top-right' ? 'top-4 right-4' : 'bottom-4 right-4' }}"
style="width: 320px;">
    
    <template x-for="toast in toasts" :key="toast.id">
        <div x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-x-8"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="flex items-center gap-3 p-4 rounded-lg shadow-lg text-white"
             :class="{
                 'bg-green-600': toast.type === 'success',
                 'bg-red-600': toast.type === 'error',
                 'bg-yellow-500': toast.type === 'warning',
                 'bg-blue-600': toast.type === 'info'
             }">
            {{-- Icon + message + close --}}
        </div>
    </template>
</div>
```

#### Step 5: Create `x-modal` component
```blade
{{-- resources/views/components/ui/modal.blade.php --}}
@props(['maxWidth' => '2xl', 'closeable' => true])

@php
$maxWidth = [
    'sm' => 'max-w-sm',
    'md' => 'max-w-md',
    'lg' => 'max-w-lg',
    'xl' => 'max-w-xl',
    '2xl' => 'max-w-2xl',
][$maxWidth];
@endphp

<div x-data="{ open: false }" @keydown.escape.window="open = false">
    {{ $trigger }}
    
    <template x-teleport="body">
        <div x-show="open" 
             x-transition.opacity
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
             style="display: none;">
            <div @click.outside="open = false"
                 x-transition:enter="ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="bg-white rounded-lg shadow-xl {{ $maxWidth }} w-full max-h-[90vh] overflow-y-auto">
                @if($closeable)
                <button @click="open = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5"><!-- X icon --></svg>
                </button>
                @endif
                {{ $slot }}
            </div>
        </div>
    </template>
</div>
```

#### Step 6: Create `x-datatable` component
```blade
{{-- resources/views/components/ui/datatable.blade.php --}}
@props(['searchable' => true, 'sortable' => true])

<div x-data="{
    search: '',
    sortCol: '',
    sortDir: 'asc',
    selected: [],
    get filtered() {
        let rows = @js($rows);
        if (this.search) {
            rows = rows.filter(r => 
                Object.values(r).some(v => 
                    String(v).toLowerCase().includes(this.search.toLowerCase())
                )
            );
        }
        if (this.sortCol) {
            rows.sort((a, b) => {
                const aVal = a[this.sortCol];
                const bVal = b[this.sortCol];
                return this.sortDir === 'asc' 
                    ? (aVal > bVal ? 1 : -1)
                    : (aVal < bVal ? 1 : -1);
            });
        }
        return rows;
    }
}">
    @if($searchable)
    <input x-model="search" type="search" placeholder="Buscar..." 
           class="mb-4 w-full md:w-64 px-3 py-2 border rounded-md">
    @endif
    
    <div class="overflow-x-auto rounded-lg border">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                {{ $header }}
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <template x-for="row in filtered" :key="row.id">
                    {{ $row }}
                </template>
            </tbody>
        </table>
    </div>
</div>
```

#### Step 7: Create `x-empty-state` component
```blade
{{-- resources/views/components/ui/empty-state.blade.php --}}
@props(['title' => 'Sin resultados', 'message' => '', 'icon' => null, 'action' => null])

<div class="flex flex-col items-center justify-center py-12 px-4 text-center">
    @if($icon)
        <div class="w-16 h-16 mb-4 text-gray-300">{{ $icon }}</div>
    @endif
    <h3 class="text-lg font-medium text-gray-900">{{ $title }}</h3>
    @if($message)
        <p class="mt-1 text-sm text-gray-500">{{ $message }}</p>
    @endif
    @if($action)
        <div class="mt-6">{{ $action }}</div>
    @endif
</div>
```

#### Step 8: Create `x-badge` component
```blade
{{-- resources/views/components/ui/badge.blade.php --}}
@props(['variant' => 'default'])

@php
$variants = [
    'default' => 'bg-gray-100 text-gray-800',
    'success' => 'bg-green-100 text-green-800',
    'warning' => 'bg-yellow-100 text-yellow-800',
    'danger'  => 'bg-red-100 text-red-800',
    'info'    => 'bg-blue-100 text-blue-800',
];
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $variants[$variant] ?? $variants['default'] }}">
    {{ $slot }}
</span>
```

#### Step 9: Create `x-loading` component
```blade
{{-- resources/views/components/ui/loading.blade.php --}}
@props(['size' => 'md'])

@php
$sizes = ['sm' => 'w-4 h-4', 'md' => 'w-8 h-8', 'lg' => 'w-12 h-12'];
@endphp

<div class="flex items-center justify-center">
    <svg class="animate-spin {{ $sizes[$size] }} text-blue-600" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
    </svg>
</div>
```

#### Step 10: Create global toast helper
```js
// resources/js/toast.js
window.toast = function(type, message) {
    window.dispatchEvent(new CustomEvent('toast', { 
        detail: { type, message } 
    }));
};

// Convenience methods
window.toast.success = (msg) => window.toast('success', msg);
window.toast.error   = (msg) => window.toast('error', msg);
window.toast.warning = (msg) => window.toast('warning', msg);
window.toast.info    = (msg) => window.toast('info', msg);
```

#### Step 11: Register components in AppServiceProvider
```php
// app/Providers/AppServiceProvider.php
use Illuminate\Support\Facades\Blade;

public function boot(): void
{
    Blade::component('ui.confirm', 'ui-confirm');
    Blade::component('ui.toast', 'ui-toast');
    Blade::component('ui.modal', 'ui-modal');
    Blade::component('ui.dropdown', 'ui-dropdown');
    Blade::component('ui.loading', 'ui-loading');
    Blade::component('ui.empty-state', 'ui-empty-state');
    Blade::component('ui.badge', 'ui-badge');
    Blade::component('ui.datatable', 'ui-datatable');
}
```

#### Step 12: Replace all SweetAlert2 usages
```blade
{{-- BEFORE: SweetAlert2 --}}
<button onclick="Swal.fire({
    title: '¿Eliminar?',
    text: 'No podrás revertir',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar'
}).then(r => r.isConfirmed && @this.destroy({{ $client->id }}))">
    Eliminar
</button>

{{-- AFTER: x-confirm --}}
<x-ui-confirm 
    title="¿Eliminar cliente?"
    message="Esta acción no se puede deshacer."
    confirm-text="Sí, eliminar"
    confirm-action="destroy"
    confirm-id="{{ $client->id }}">
    <x-slot:trigger>
        <button class="text-red-600 hover:text-red-800">Eliminar</button>
    </x-slot:trigger>
</x-ui-confirm>
```

#### Step 13: Add toast container to layout
```blade
{{-- resources/views/layouts/app.blade.php — add once, globally --}}
<x-ui-toast position="top-right" />
```

#### Step 14: Update Livewire components to use toast
```php
// In any Livewire component action:
public function destroy($id)
{
    Client::findOrFail($id)->delete();
    $this->dispatch('toast', type: 'success', message: 'Cliente eliminado');
}
```

**Files affected**:
| Action | Files |
|--------|-------|
| DELETE | SweetAlert2 CDN line in `layouts/app.blade.php` |
| CREATE | 8 component files in `resources/views/components/ui/` |
| CREATE | `resources/js/toast.js` helper |
| MODIFY | `app/Providers/AppServiceProvider.php` — register components |
| MODIFY | `resources/js/app.js` — import toast helper |
| MODIFY | 16 view files — replace `Swal.fire()` with `<x-ui-confirm>` |
| MODIFY | Livewire components — use `$this->dispatch('toast', ...)` |

**Story points**: 8

---

### P0-3: Emails Transaccionales Habilitados

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

### P0-4: Fix — Generación Automática de Cuotas (Día 1 del mes)

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

// Contracts
test('cannot delete contract that has clients associated');
test('can update plan megabytes and price');

// Quotas
test('quota creation sends notification to admin');
test('quota generation dispatches payment job for all clients');
test('cannot generate duplicate quota for same month-year');

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
public function update(User $user, Client $client): bool
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
- `app/Policies/ClientPolicy.php`, `ContractPolicy.php`, `QuotaPolicy.php`, `PaymentPolicy.php`
- Update controllers → `authorize()` calls en cada action

**Story points**: 8

---

### P2-1: Email Template Library (Tailwind CSS Components Reusables)

**Current**: `resources/views/emails/quota-generated.blade.php` existe pero hardcoded.

**Goal**: Librería modular de email templates con Tailwind CSS compatible (SparkPost, Mailgun ready).

**Template list**:
```
components/emails/header.blade.php
components/emails/footer.blade.php
components/emails/button.blade.php
components/emails/table.blade.php

emails/quota-generated.html → usa components above
emails/payment-overdue.html
emails/client-suspended.html
emails/welcome.html
```

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

**Current**: Controllers return Models directly, no desacoplamiento.

**Goal**: JsonResource wrappers para responses consistentes + transformación de datos.

```php
class ClientResource extends JsonResource {
    public function toArray($request) {
        return [
            'id' => $this->id,
            'name' => "{$this->nombre} {$this->apellido}",
            'email' => $this->email,
            'contract_name' => $this->contract()->nombre,
        ];
    }
}
```

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

### Real-time Notifications con Echo + Redis

**Goal**: Live updates sin refresh page (cuotas generadas, pagos registrados, nuevos clientes).

**Backend**: Laravel Echo + Pusher o Redis broadcasting

```php
// In QuotaCreated event:
event(new QuotaCreated($quota));
broadcast(new NotificationEvent(...))->toOthers();
```

**Frontend**: Laravel Echo integration in app.blade.php — `echo.channel('notifications')`

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

### Phase 1 — Critical Fixes + UI Foundation (Weeks 1-2)

| Day | Task |
|-----|------|
| 1-2 | UI Components: create 8 Alpine.js components + toast helper |
| 3-4 | Replace all 16 SweetAlert2 usages with x-confirm |
| 5 | Migrations domain tables + test `migrate:fresh` |
| 6-7 | Fix quota generation consolidation (Service layer) |
| 8 | Add email column to clients |
| 9-10 | Notifications system (migration + classes + bell UI) |
| Weekend | Review + documentation |

**Deliverables**: UI components propios funcionando, SweetAlert2 eliminado, migrations completas, notifications visibles.

---

### Phase 2 — Test Coverage + Emails (Weeks 3-4)

| Day | Task |
|-----|------|
| 1-3 | Client tests (create, update, ban/unban, export PDFs) |
| 4-5 | Contract + AccessPoint tests |
| 6-7 | Quota + Payment service tests |
| 8-9 | SMTP config + enable email notifications |
| 10 | Coverage report, fix failures |

**Deliverables**: ≥50% test coverage, GitHub Actions CI pipeline running, emails funcionando.

---

### Phase 3 — Security + Polish (Weeks 5-6)

| Day | Task |
|-----|------|
| 1-3 | Policies + Gates implementation |
| 4-5 | Role column in users table + seed sample roles |
| 6-7 | Email template library + observers |

**Deliverables**: Authorization layer completo, role-based access control, audit logging.

---

## 🧾 Checklist v2.0 — Acceptance Criteria

### P0 — Critical Path ✅

- [ ] UI Components propia (8 components Alpine.js) — cero SweetAlert2
- [ ] `x-confirm` reemplaza los 16 `Swal.fire()` en todas las vistas
- [ ] `x-toast` global container en layout + helper `window.toast()`
- [ ] Notifications system (database + bell UI en navbar)
- [ ] Migrations domain tables completas
- [ ] `php artisan migrate:fresh --seed` funciona end-to-end
- [ ] Cuotas se generan automáticamente día 1 del mes
- [ ] Emails enviados correctamente → test con SMTP sandbox

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
| TD-02 | SweetAlert2 CDN + 16 duplicated Swal.fire() | Open | P0 | Replaced by UI components propia |
| TD-03 | Empty userController methods | Open | P3 | Can be deleted or stubbed |
| TD-04 | Payment controller index/create/store/destroy empty | Open | P2 | Implement or remove routes |
| TD-05 | Dual Tailwind v3 + v4 | Open | P3 | Migrate to v4 only |
| TD-06 | `db:seed` in docker-compose auto-start | Open | P1 | Change to conditional seed |
| TD-07 | No foreign key indexes | Open | P1 | Add indexes on FK columns |

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
- UI Components usage guide (storybook-style examples)

---

## 🎬 Next Steps

1. **Create issue tracker** para cada P0/P1 task con acceptance criteria
2. **Setup staging environment** con fresh migration pipeline
3. **Backup production data** antes de cambios estructurales
4. **Enable GitHub Actions CI** con test suite (verificar green)
5. **Schedule deployment** para v2.0 — target: 6 semanas

---

**Document version**: 2.0
**Last updated**: August 2026
**Author**: Architect Team + User Requirements (notifications, emails, quota scheduling, UI components)
