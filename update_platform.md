# Update Platform - ISP Manager

## Resumen de Cambios

Este documento describe la implementación de nuevos módulos y mejoras en el sistema ISP-Manager.

### Módulos Nuevos
1. **Proveedores** - Gestión de proveedores de internet/equipamiento
2. **Gastos** - Registro de gastos operativos de la empresa
3. **Métricas de Rentabilidad** - Análisis de costo vs ingreso

### Mejoras Existentes
4. **Filtros en Clientes** - Búsqueda y filtrado avanzado
5. **Paginación Livewire en Clientes** - Mejor rendimiento con grandes volúmenes
6. **API REST** - Endpoints para futura app móvil
7. **Audit Log** - Registro de cambios críticos

---

## Fase 1: Base de Datos (Migraciones)

### 1.1 Tabla `proveedores` (Providers)

```php
// database/migrations/xxxx_create_proveedores_table.php
Schema::create('proveedores', function (Blueprint $table) {
    $table->id();
    $table->string('nombre', 150);
    $table->string('contacto', 100)->nullable();
    $table->string('telefono', 20)->nullable();
    $table->string('email', 100)->nullable();
    $table->string('direccion', 255)->nullable();
    
    // Ancho de banda
    $table->integer('mb_up');          // Megabytes upload
    $table->integer('mb_down');        // Megabytes download
    
    // Precios
    $table->decimal('precio_total', 12, 2);      // Precio mensual total
    $table->decimal('precio_por_mb', 10, 4);     // Precio por megabyte
    
    // Tipo de proveedor
    $table->enum('tipo', ['internet', 'equipamiento', 'ambos'])->default('internet');
    
    $table->text('notas')->nullable();
    $table->boolean('activo')->default(true);
    $table->timestamps();
});
```

### 1.2 Tabla `gastos` (Expenses)

```php
// database/migrations/xxxx_create_gastos_table.php
Schema::create('gastos', function (Blueprint $table) {
    $table->id();
    $table->string('concepto', 200);           // Descripción del gasto
    $table->enum('categoria', [
        'cables_utp',
        'herramientas',
        'rj45',
        'routers_clientes',
        'equipos_nodos',
        'fibra_optica',
        'antenas',
        'postes_torres',
        'combustible',
        'salarios',
        'alquiler',
        'servicios',
        'reparaciones',
        'otros'
    ]);
    $table->decimal('monto', 12, 2);
    $table->date('fecha_gasto');
    $table->string('proveedor', 150)->nullable();  // Proveedor relacionado
    $table->string('comprobante', 255)->nullable(); // Path del archivo
    $table->text('notas')->nullable();
    
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->timestamps();
});
```

### 1.3 Tabla `audit_logs` (Registro de Auditoría)

```php
// database/migrations/xxxx_create_audit_logs_table.php
Schema::create('audit_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
    $table->string('model_type', 100);          // App\Models\Payments, etc
    $table->unsignedBigInteger('model_id');
    $table->enum('accion', ['created', 'updated', 'deleted']);
    $table->json('valores_anteriores')->nullable();
    $table->json('valores_nuevos')->nullable();
    $table->string('ip_address', 45)->nullable();
    $table->timestamp('created_at');
});
```

### 1.4 Mejoras a tablas existentes

```php
// Agregar columna a pagos para tracking de pago parcial
// database/migrations/xxxx_add_partial_payment_to_pagos.php
Schema::table('pagos', function (Blueprint $table) {
    $table->decimal('pago_parcial', 10, 2)->default(0)->after('abonado');
    $table->date('fecha_vencimiento')->nullable()->after('fecha_pago');
    $table->index(['estado', 'created_at']);
});
```

---

## Fase 2: Modelos Eloquent

### 2.1 Proveedor.php

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'proveedores';

    protected $fillable = [
        'nombre', 'contacto', 'telefono', 'email', 'direccion',
        'mb_up', 'mb_down', 'precio_total', 'precio_por_mb',
        'tipo', 'notas', 'activo'
    ];

    protected $casts = [
        'precio_total' => 'decimal:2',
        'precio_por_mb' => 'decimal:4',
        'activo' => 'boolean',
    ];

    // Scope para proveedores activos
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    // Cálculo de costo efectivo por MB
    public function getCostoEfectivoAttribute(): float
    {
        $totalMb = $this->mb_down + $this->mb_up;
        return $totalMb > 0 ? $this->precio_total / $totalMb : 0;
    }
}
```

### 2.2 Gasto.php

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gasto extends Model
{
    protected $table = 'gastos';

    protected $fillable = [
        'concepto', 'categoria', 'monto', 'fecha_gasto',
        'proveedor', 'comprobante', 'notas', 'user_id'
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_gasto' => 'date',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Scope para filtrar por categoría
    public function scopeCategoria($query, string $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    // Scope para filtrar por rango de fechas
    public function scopeEntreFechas($query, $desde, $hasta)
    {
        return $query->whereBetween('fecha_gasto', [$desde, $hasta]);
    }
}
```

### 2.3 AuditLog.php

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $table = 'audit_logs';

    protected $fillable = [
        'user_id', 'model_type', 'model_id', 'accion',
        'valores_anteriores', 'valores_nuevos', 'ip_address', 'created_at'
    ];

    protected $casts = [
        'valores_anteriores' => 'array',
        'valores_nuevos' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function model()
    {
        return $this->morphTo();
    }
}
```

---

## Fase 3: Controladores

### 3.1 ProveedorController.php

```php
<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    public function index()
    {
        $proveedores = Proveedor::orderBy('nombre')->paginate(15);
        return view('proveedores.index', compact('proveedores'));
    }

    public function create()
    {
        return view('proveedores.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:150',
            'contacto' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'direccion' => 'nullable|string|max:255',
            'mb_up' => 'required|integer|min:0',
            'mb_down' => 'required|integer|min:0',
            'precio_total' => 'required|numeric|min:0',
            'precio_por_mb' => 'nullable|numeric|min:0',
            'tipo' => 'required|in:internet,equipamiento,ambos',
            'notas' => 'nullable|string',
        ]);

        Proveedor::create($validated);
        return redirect()->route('proveedores.index')->with('success', 'Proveedor creado correctamente.');
    }

    public function show(string $id)
    {
        $proveedor = Proveedor::findOrFail($id);
        return view('proveedores.show', compact('proveedor'));
    }

    public function edit(string $id)
    {
        $proveedor = Proveedor::findOrFail($id);
        return view('proveedores.edit', compact('proveedor'));
    }

    public function update(Request $request, string $id)
    {
        $proveedor = Proveedor::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:150',
            'contacto' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:100',
            'direccion' => 'nullable|string|max:255',
            'mb_up' => 'required|integer|min:0',
            'mb_down' => 'required|integer|min:0',
            'precio_total' => 'required|numeric|min:0',
            'precio_por_mb' => 'nullable|numeric|min:0',
            'tipo' => 'required|in:internet,equipamiento,ambos',
            'notas' => 'nullable|string',
            'activo' => 'boolean',
        ]);

        $proveedor->update($validated);
        return redirect()->route('proveedores.show', $id)->with('success', 'Proveedor actualizado.');
    }

    public function destroy(string $id)
    {
        Proveedor::findOrFail($id)->delete();
        return redirect()->route('proveedores.index')->with('success', 'Proveedor eliminado.');
    }
}
```

### 3.2 GastoController.php

```php
<?php

namespace App\Http\Controllers;

use App\Models\Gasto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GastoController extends Controller
{
    public function index(Request $request)
    {
        $query = Gasto::with('usuario');

        if ($request->filled('categoria')) {
            $query->categoria($request->categoria);
        }

        if ($request->filled('desde') && $request->filled('hasta')) {
            $query->entreFechas($request->desde, $request->hasta);
        }

        $gastos = $query->orderBy('fecha_gasto', 'desc')->paginate(20);
        $categorias = [
            'cables_utp' => 'Cables UTP',
            'herramientas' => 'Herramientas',
            'rj45' => 'RJ45',
            'routers_clientes' => 'Routers para Clientes',
            'equipos_nodos' => 'Equipos para Nodos',
            'fibra_optica' => 'Fibra Óptica',
            'antenas' => 'Antenas',
            'postes_torres' => 'Postes/Torres',
            'combustible' => 'Combustible',
            'salarios' => 'Salarios',
            'alquiler' => 'Alquiler',
            'servicios' => 'Servicios',
            'reparaciones' => 'Reparaciones',
            'otros' => 'Otros',
        ];

        return view('gastos.index', compact('gastos', 'categorias'));
    }

    public function create()
    {
        return view('gastos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'concepto' => 'required|string|max:200',
            'categoria' => 'required|in:cables_utp,herramientas,rj45,routers_clientes,equipos_nodos,fibra_optica,antenas,postes_torres,combustible,salarios,alquiler,servicios,reparaciones,otros',
            'monto' => 'required|numeric|min:0',
            'fecha_gasto' => 'required|date',
            'proveedor' => 'nullable|string|max:150',
            'comprobante' => 'nullable|file|mimes:jpg,png,pdf|max:4096',
            'notas' => 'nullable|string',
        ]);

        if ($request->hasFile('comprobante')) {
            $path = $request->file('comprobante')->store('comprobantes', 'public');
            $validated['comprobante'] = $path;
        }

        $validated['user_id'] = auth()->id();
        Gasto::create($validated);

        return redirect()->route('gastos.index')->with('success', 'Gasto registrado.');
    }

    public function edit(string $id)
    {
        $gasto = Gasto::findOrFail($id);
        return view('gastos.edit', compact('gasto'));
    }

    public function update(Request $request, string $id)
    {
        $gasto = Gasto::findOrFail($id);

        $validated = $request->validate([
            'concepto' => 'required|string|max:200',
            'categoria' => 'required|in:cables_utp,herramientas,rj45,routers_clientes,equipos_nodos,fibra_optica,antenas,postes_torres,combustible,salarios,alquiler,servicios,reparaciones,otros',
            'monto' => 'required|numeric|min:0',
            'fecha_gasto' => 'required|date',
            'proveedor' => 'nullable|string|max:150',
            'comprobante' => 'nullable|file|mimes:jpg,png,pdf|max:4096',
            'notas' => 'nullable|string',
        ]);

        if ($request->hasFile('comprobante')) {
            if ($gasto->comprobante) {
                Storage::disk('public')->delete($gasto->comprobante);
            }
            $path = $request->file('comprobante')->store('comprobantes', 'public');
            $validated['comprobante'] = $path;
        }

        $gasto->update($validated);
        return redirect()->route('gastos.index')->with('success', 'Gasto actualizado.');
    }

    public function destroy(string $id)
    {
        $gasto = Gasto::findOrFail($id);
        if ($gasto->comprobante) {
            Storage::disk('public')->delete($gasto->comprobante);
        }
        $gasto->delete();
        return redirect()->route('gastos.index')->with('success', 'Gasto eliminado.');
    }
}
```

---

## Fase 4: Vistas Blade

### Estructura de carpetas a crear

```
resources/views/
├── proveedores/
│   ├── index.blade.php
│   ├── create.blade.php
│   ├── edit.blade.php
│   └── show.blade.php
├── gastos/
│   ├── index.blade.php
│   ├── create.blade.php
│   └── edit.blade.php
└── livewire/
    ├── proveedores-table.blade.php
    └── gastos-table.blade.php
```

---

## Fase 5: Rutas

```php
// routes/web.php

// Proveedores
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified', 'throttle:auth-users'])
    ->prefix('proveedores')
    ->group(function () {
        Route::get('/', [ProveedorController::class, 'index'])->name('proveedores.index');
        Route::get('/create', [ProveedorController::class, 'create'])->name('proveedores.create');
        Route::post('/', [ProveedorController::class, 'store'])->name('proveedores.store');
        Route::get('/{id}', [ProveedorController::class, 'show'])->name('proveedores.show');
        Route::get('/{id}/edit', [ProveedorController::class, 'edit'])->name('proveedores.edit');
        Route::put('/{id}', [ProveedorController::class, 'update'])->name('proveedores.update');
        Route::delete('/{id}', [ProveedorController::class, 'destroy'])->name('proveedores.destroy');
    });

// Gastos
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified', 'throttle:auth-users'])
    ->prefix('gastos')
    ->group(function () {
        Route::get('/', [GastoController::class, 'index'])->name('gastos.index');
        Route::get('/create', [GastoController::class, 'create'])->name('gastos.create');
        Route::post('/', [GastoController::class, 'store'])->name('gastos.store');
        Route::get('/{id}/edit', [GastoController::class, 'edit'])->name('gastos.edit');
        Route::put('/{id}', [GastoController::class, 'update'])->name('gastos.update');
        Route::delete('/{id}', [GastoController::class, 'destroy'])->name('gastos.destroy');
    });

// Métricas de Rentabilidad (agregar al dashboard existente)
Route::get('/api/metrics/profitability', [DashboardController::class, 'profitabilityMetrics'])->name('metrics.profitability');
```

---

## Fase 6: Métricas de Rentabilidad

### DashboardController.php (nuevo método)

```php
public function profitabilityMetrics(Request $request)
{
    $anio = $request->get('anio', now()->year);
    $mes = $request->get('mes', now()->month);

    // Ingresos del mes
    $ingresos = Payments::whereYear('fecha_pago', $anio)
        ->whereMonth('fecha_pago', $mes)
        ->sum('abonado');

    // Costos de proveedores
    $costoProveedores = Proveedor::activos()->sum('precio_total');

    // Gastos del mes
    $gastos = Gasto::whereYear('fecha_gasto', $anio)
        ->whereMonth('fecha_gasto', $mes)
        ->sum('monto');

    // Totales
    $costoTotal = $costoProveedores + $gastos;
    $gananciaNeta = $ingresos - $costoTotal;
    $margen = $ingresos > 0 ? ($gananciaNeta / $ingresos) * 100 : 0;

    return response()->json([
        'ingresos' => $ingresos,
        'costo_proveedores' => $costoProveedores,
        'gastos_operativos' => $gastos,
        'costo_total' => $costoTotal,
        'ganancia_neta' => $gananciaNeta,
        'margen_porcentaje' => round($margen, 2),
        'es_rentable' => $gananciaNeta > 0,
    ]);
}
```

---

## Fase 7: Audit Log (Trait Reutilizable)

### app/Traits/Auditable.php

```php
<?php

namespace App\Traits;

use App\Models\AuditLog;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function ($model) {
            self::logChange($model, 'created');
        });

        static::updated(function ($model) {
            self::logChange($model, 'updated');
        });

        static::deleted(function ($model) {
            self::logChange($model, 'deleted');
        });
    }

    protected static function logChange($model, string $accion)
    {
        $userId = auth()->id();
        if (!$userId && $accion === 'created') return;

        AuditLog::create([
            'user_id' => $userId,
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'accion' => $accion,
            'valores_anteriores' => $accion !== 'created' ? $model->getOriginal() : null,
            'valores_nuevos' => $accion !== 'deleted' ? $model->getAttributes() : null,
            'ip_address' => request()->ip(),
            'created_at' => now(),
        ]);
    }
}
```

### Uso en modelos

```php
// En Payment model
use App\Traits\Auditable;

class Payments extends Model
{
    use Auditable;
    // ...
}
```

---

## Fase 8: API REST

### routes/api.php

```php
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ProveedorController;

Route::middleware('auth:sanctum')->group(function () {
    // Clientes
    Route::apiResource('clients', ClientController::class);
    Route::get('clients/{id}/payments', [ClientController::class, 'payments']);
    
    // Pagos
    Route::apiResource('payments', PaymentController::class);
    Route::post('payments/{id}/pay', [PaymentController::class, 'markAsPaid']);
    
    // Proveedores
    Route::apiResource('providers', ProveedorController::class);
    
    // Métricas
    Route::get('metrics/dashboard', [DashboardApiController::class, 'index']);
});
```

---

## Fase 9: Mejoras en Clientes (Filtros + Paginación Livewire)

### ClientFilter.php (Livewire)

```php
<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Client;
use App\Models\Contracts;

class ClientFilter extends Component
{
    use WithPagination;

    public $search = '';
    public $planId = '';
    public $status = ''; // '', 'active', 'debtor', 'banned'

    public function render()
    {
        $query = Client::with(['contract', 'pagos']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nombre', 'like', "%{$this->search}%")
                  ->orWhere('apellido', 'like', "%{$this->search}%")
                  ->orWhere('ip', 'like', "%{$this->search}%");
            });
        }

        if ($this->planId) {
            $query->where('id_plan', $this->planId);
        }

        if ($this->status === 'debtor') {
            $query->whereHas('pagos', fn($q) => $q->where('estado', 0));
        } elseif ($this->status === 'banned') {
            $query->where('is_banned', true);
        } elseif ($this->status === 'active') {
            $query->where('is_banned', false);
        }

        $clients = $query->orderBy('apellido')->paginate(20);
        $plans = Contracts::all();

        return view('livewire.client-filter', compact('clients', 'plans'));
    }
}
```

---

## Orden de Implementación

| Paso | Tarea | Tiempo Estimado |
|------|-------|-----------------|
| 1 | Crear migraciones y ejecutarlas | 30 min |
| 2 | Crear modelos (Proveedor, Gasto, AuditLog) | 20 min |
| 3 | Crear controladores (Proveedor, Gasto) | 40 min |
| 4 | Crear vistas de Proveedores | 1 hora |
| 5 | Crear vistas de Gastos | 1 hora |
| 6 | Agregar rutas | 15 min |
| 7 | Implementar métricas de rentabilidad | 30 min |
| 8 | Implementar Audit Log | 30 min |
| 9 | Implementar API REST | 1 hora |
| 10 | Implementar filtros Livewire en clientes | 45 min |
| 11 | Actualizar navegación/menú | 15 min |
| **Total** | | **~7 horas** |

---

## Notas Importantes

1. **Backup**: Hacer backup completo de la base de datos antes de ejecutar migraciones
2. **índices**: Agregar índices a columnas frecuentemente buscadas
3. **Cache**: Invalidar cache de estadísticas cuando se registren gastos
4. **Seeders**: Crear seeders para categorías de gastos y proveedores de ejemplo
5. **Tests**: Agregar feature tests para los nuevos endpoints

---

## Comandos para Ejecutar

```bash
# Crear todo con artisan
php artisan make:model Proveedor -m
php artisan make:model Gasto -m
php artisan make:model AuditLog -m
php artisan make:controller ProveedorController --resource
php artisan make:controller GastoController --resource
php artisan make:livewire ClientFilter

# Ejecutar migraciones
php artisan migrate

# Limpiar cache después de cambios
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

---

## Estado de Implementaci�n

### ? Completado

| Componente | Archivos |
|-----------|----------|
| Migraciones | 4 migraciones creadas (proveedores, gastos, audit_logs, improve_pagos) |
| Modelos | Proveedor.php, Gasto.php, AuditLog.php |
| Trait Auditable | app/Traits/Auditable.php |
| Controladores | ProveedorController.php, GastoController.php, DashboardMetricsController.php |
| API Controllers | PaymentApiController.php, DashboardApiController.php |
| Vistas Proveedores | index, create, edit, show |
| Vistas Gastos | index, create, edit |
| Livewire | ClientFilter.php + vista |
| Dashboard | M�tricas de rentabilidad integradas |
| Navegaci�n | Proveedores y Gastos agregados al men� |
| Rutas | web.php y api.php actualizados |
| Payments Model | Auditable trait + casts + nuevos campos |

### Pendiente en Producci�n

1. **Ejecutar migraciones:**
   `ash
   php artisan migrate
   `

2. **Configurar variables de entorno (.env):**
   `env
   QUOTA_NOTIFICATION_EMAILS=admin@tudominio.com
   `

3. **Configurar cron (scheduler):**
   `
   * * * * * cd /ruta && php artisan schedule:run >> /dev/null 2>&1
   `

4. **Configurar Supervisor (worker):**
   `ash
   php artisan queue:work --tries=3 --timeout=300
   `

5. **Limpiar cache:**
   `ash
   php artisan cache:clear
   php artisan config:clear
   php artisan view:clear
   `

### Endpoints Nuevos

| M�todo | URL | Descripci�n |
|--------|-----|-------------|
| GET | /proveedores | Listar proveedores |
| POST | /proveedores | Crear proveedor |
| GET | /proveedores/{id} | Ver proveedor |
| PUT | /proveedores/{id} | Actualizar proveedor |
| DELETE | /proveedores/{id} | Eliminar proveedor |
| GET | /gastos | Listar gastos |
| POST | /gastos | Crear gasto |
| PUT | /gastos/{id} | Actualizar gasto |
| DELETE | /gastos/{id} | Eliminar gasto |
| POST | /payments/retry | Reintentar pagos fallidos |
| GET | /payments/export/pdf | Exportar pagos a PDF |
| GET | /api/metrics/profitability | M�tricas de rentabilidad |
| GET | /api/payments | API: Listar pagos |
| GET | /api/payments/{id} | API: Ver pago |
| PUT | /api/payments/{id} | API: Actualizar pago |
| GET | /api/dashboard/metrics | API: M�tricas dashboard |

### Categor�as de Gastos Disponibles

- cables_utp
- herramientas
- rj45
- routers_clientes
- equipos_nodos
- fibra_optica
- antenas
- postes_torres
- combustible
- salarios
- alquiler
- servicios
- reparaciones
- otros
