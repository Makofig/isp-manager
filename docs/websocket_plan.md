# WebSocket Implementation Plan - ISP Manager

## Objetivo

Mantener estados sincronizados entre múltiples sesiones de usuarios usando WebSockets, para que todos los administradores vean actualizaciones en tiempo real sin necesidad de polling.

## Arquitectura

```
┌─────────────────────────────────────────────────────────────────┐
│                        ISP Manager                               │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  ┌──────────┐     ┌──────────────┐     ┌───────────────────┐   │
│  │  Laravel  │────▶│  Broadcasting │────▶│  Laravel Reverb   │   │
│  │  Events   │     │  (Reverb)     │     │  (WebSocket Server)│   │
│  └──────────┘     └──────────────┘     └───────────────────┘   │
│                                               │                  │
│                                               │                  │
│  ┌────────────────────────────────────────────┘                  │
│  │                                                               │
│  ▼                                                               │
│  ┌──────────────┐                                                │
│  │  Laravel Echo │  ◄── Frontend (JavaScript)                   │
│  │  (Client)     │                                                │
│  └──────────────┘                                                │
│                                                                  │
│  ┌──────────────┐     ┌──────────────┐                          │
│  │  Browser A    │     │  Browser B    │  ← Ambos ven el mismo  │
│  │  (Admin 1)    │     │  (Admin 2)    │     estado en tiempo real│
│  └──────────────┘     └──────────────┘                          │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

## Casos de Uso

### 1. Progreso de Generación de Cuotas
- Cuando se crea una cuota, todos los admins ven el progreso en tiempo real
- Elimina la necesidad de `wire:poll` en QuotaCreate

### 2. Notificaciones de Pagos
- Cuando se registra un pago, el dashboard se actualiza automáticamente
- Notificación visual sin recargar la página

### 3. Sincronización de Dashboard
- Métricas de rentabilidad se actualizan cuando se registran gastos/proveedores
- Todos los admins ven los mismos números

### 4. Alertas del Sistema
- Notificaciones de errores en Jobs
- Avisos de cuotas generadas automáticamente

## Stack Tecnológico

| Componente | Tecnología |
|-----------|-----------|
| Servidor WebSocket | Laravel Reverb |
| Protocolo | Pusher Protocol v7 |
| Cliente Frontend | Laravel Echo |
| Broadcasting Driver | `reverb` (nativo) |
| Procesamiento | Events + Jobs |

## Paso a Paso

### Paso 1: Instalación

```bash
composer require laravel/reverb
php artisan reverb:install
```

### Paso 2: Configuración

#### .env
```env
BROADCAST_DRIVER=reverb
REVERB_APP_ID=isp-manager
REVERB_APP_KEY=generated_key
REVERB_APP_SECRET=generated_secret
REVERB_HOST="localhost"
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

#### config/broadcasting.php
```php
'connections' => [
    'reverb' => [
        'driver' => 'reverb',
        'key' => env('REVERB_APP_KEY'),
        'secret' => env('REVERB_APP_SECRET'),
        'app_id' => env('REVERB_APP_ID'),
        'options' => [
            'host' => env('REVERB_HOST'),
            'port' => env('REVERB_PORT', 443),
            'scheme' => env('REVERB_SCHEME', 'https'),
            'useTLS' => env('REVERB_SCHEME', 'https') === 'https',
        ],
        'client_options' => [],
    ],
],
```

### Paso 3: Eventos a Implementar

#### 1. QuotaProgressUpdated
Se emite cuando el Job actualiza el progreso.

```php
class QuotaProgressUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $quotaId;
    public $progress;

    public function __construct($quotaId, $progress)
    {
        $this->quotaId = $quotaId;
        $this->progress = $progress;
    }

    public function broadcastOn()
    {
        return new Channel('quota.' . $this->quotaId);
    }

    public function broadcastAs()
    {
        return 'progress.updated';
    }
}
```

#### 2. PaymentRegistered
Se emite cuando se registra un pago.

```php
class PaymentRegistered implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $payment;
    public $clientId;

    public function __construct(Payments $payment)
    {
        $this->payment = $payment;
        $this->clientId = $payment->id_cliente;
    }

    public function broadcastOn()
    {
        return new Channel('dashboard');
    }
}
```

#### 3. ExpenseRegistered
Se emite cuando se registra un gasto.

```php
class ExpenseRegistered implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $expense;

    public function __construct(Gasto $expense)
    {
        $this->expense = $expense;
    }

    public function broadcastOn()
    {
        return new Channel('dashboard');
    }
}
```

#### 4. ProviderUpdated
Se emite cuando se crea/actualiza un proveedor.

```php
class ProviderUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $provider;

    public function __construct(Proveedor $provider)
    {
        $this->provider = $provider;
    }

    public function broadcastOn()
    {
        return new Channel('dashboard');
    }
}
```

### Paso 4: Integración en Jobs

#### GenerateQuotaPayments.php (actualización)
```php
// Dentro del chunk(), después de calcular progress:
if ($progress % 10 === 0 || $progress >= 100) {
    QuotaProgressUpdated::dispatch($this->quotaId, $progress);
}
```

### Paso 5: Frontend (Laravel Echo)

#### resources/js/echo.js
```javascript
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});
```

#### Vistas
```javascript
// En quota-create.blade.php
Echo.channel('quota.' + this.quotaId)
    .listen('ProgressUpdated', (e) => {
        this.progress = e.progress;
        if (e.progress >= 100) {
            this.loading = false;
        }
    });

// En dashboard
Echo.channel('dashboard')
    .listen('PaymentRegistered', (e) => {
        // Actualizar métricas
    })
    .listen('ExpenseRegistered', (e) => {
        // Actualizar métricas
    });
```

### Paso 6: Ejecución en Producción

#### Supervisor para Reverb
```ini
; /etc/supervisor/conf.d/reverb.conf
[program:reverb]
command=php /ruta/al/proyecto/artisan reverb:start --host="0.0.0.0" --port=8080
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/ruta/al/proyecto/storage/logs/reverb.log
```

#### Nginx Reverse Proxy (opcional pero recomendado)
```nginx
location /app/ {
    proxy_http_version 1.1;
    proxy_set_header Host $http_host;
    proxy_set_header Scheme $scheme;
    proxy_set_header SERVER_PORT $server_port;
    proxy_set_header REMOTE_ADDR $remote_addr;
    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection "Upgrade";

    proxy_pass http://127.0.0.1:8080;
}
```

## Canales

| Canal | Tipo | Uso |
|-------|------|-----|
| `quota.{id}` | Channel | Progreso de generación de cuota específica |
| `dashboard` | Channel | Actualizaciones globales del dashboard |
| `notifications` | Private Channel | Notificaciones por usuario |

## Seguridad

### Canales Privados
Para canales que requieren autenticación:

```php
// routes/channels.php
Broadcast::channel('notifications.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
```

```javascript
// Frontend
Echo.private('notifications.' + userId)
    .listen('Notification', (e) => { ... });
```

## Migración desde Polling

### Antes (Polling)
```html
<div wire:poll.1000ms="checkProgress">
    <!-- Barra de progreso -->
</div>
```

### Después (WebSockets)
```html
<div x-data="{ progress: 0, loading: true }"
     x-init="
         Echo.channel('quota.' + quotaId)
             .listen('ProgressUpdated', (e) => {
                 progress = e.progress;
                 if (e.progress >= 100) loading = false;
             });
     ">
    <!-- Barra de progreso reactiva -->
</div>
```

## Ventajas

1. **Menos carga en servidor**: No hay requests repetidos cada segundo
2. **Actualización instantánea**: Los cambios se reflejan inmediatamente
3. **Escalabilidad**: Reverb maneja miles de conexiones simultáneas
4. **Multi-sesión**: Todos los admins ven el mismo estado
5. **Eficiencia**: Solo se transmite cuando hay cambios reales

## Plan de Implementación

| Fase | Tarea | Tiempo |
|------|-------|--------|
| 1 | Instalar Reverb + Configurar .env | 15 min |
| 2 | Crear Eventos (QuotaProgress, Payment, Expense, Provider) | 30 min |
| 3 | Integrar Eventos en Jobs y Controladores | 30 min |
| 4 | Configurar Laravel Echo en frontend | 20 min |
| 5 | Actualizar vistas (quota-create, dashboard) | 45 min |
| 6 | Configurar Supervisor para Reverb | 15 min |
| 7 | Testing y validación | 30 min |
| **Total** | | **~3 horas** |

---

## Estado de Implementación

### ✅ Completado

| Componente | Archivos |
|-----------|----------|
| Eventos | QuotaProgressUpdated.php, PaymentRegistered.php, ExpenseRegistered.php, ProviderUpdated.php |
| Config Reverb | config/reverb.php, config/broadcasting.php |
| Frontend Echo | resources/js/echo.js, resources/js/app.js |
| Vite Config | vite.config.js (echo.js incluido) |
| Layout | app.blade.php (listeners globales dashboard) |
| QuotaCreate | Vista actualizada (WebSocket en lugar de polling) |
| Dashboard | Livewire component + vista (refresh automático) |
| Job Integration | GenerateQuotaPayments emite QuotaProgressUpdated |
| Controller Integration | ProveedorController y GastoController emiten eventos |

### Pendiente en Producción

1. **Instalar Laravel Reverb:**
   ```bash
   composer require laravel/reverb
   php artisan reverb:install
   ```

2. **Configurar .env:**
   ```env
   BROADCAST_CONNECTION=reverb
   REVERB_APP_ID=isp-manager
   REVERB_APP_KEY=your-app-key
   REVERB_APP_SECRET=your-app-secret
   REVERB_HOST="localhost"
   REVERB_PORT=8080
   REVERB_SCHEME=http

   VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
   VITE_REVERB_HOST="${REVERB_HOST}"
   VITE_REVERB_PORT="${REVERB_PORT}"
   VITE_REVERB_SCHEME="${REVERB_SCHEME}"
   ```

3. **Configurar Supervisor para Reverb:**
   ```ini
   [program:reverb]
   command=php /ruta/artisan reverb:start --host="0.0.0.0" --port=8080
   autostart=true
   autorestart=true
   user=www-data
   redirect_stderr=true
   stdout_logfile=/ruta/storage/logs/reverb.log
   ```

4. **Compilar assets:**
   ```bash
   npm run build
   ```

5. **Reiniciar servicios:**
   ```bash
   sudo supervisorctl restart all
   php artisan queue:restart
   ```
