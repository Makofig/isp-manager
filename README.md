# InterSys — Network Management Platform

[![PHP Version](https://img.shields.io/badge/php-8.3%2B-blue)](https://php.net)
[![Laravel](https://img.shields.io/badge/laravel-12.x-red)](https://laravel.com)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)

Sistema de gestión ISP para administrar clientes, contratos, cuotas mensuales, puntos de acceso y pagos.

## Características

- **Gestión de Clientes**: CRUD completo, sistema de ban/unban, control de deudores
- **Contratos**: Planes de internet con ancho de banda y precios
- **Cuotas**: Generación automática mensual, notificaciones por email
- **Puntos de Acceso**: Monitoreo de APs y clientes conectados
- **Pagos**: Registro de pagos, comprobantes (vouchers), historial
- **Notificaciones**: Sistema in-app + emails transaccionales
- **Roles**: Admin, Operator, Auditor con políticas de acceso
- **Dashboard**: Métricas, gráficos de ingresos y actividad

## Stack

| Componente | Tecnología |
|------------|------------|
| Backend | Laravel 12 + PHP 8.3 |
| Frontend | Livewire 3 + Alpine.js + Tailwind CSS |
| Database | MySQL 8.0 |
| Auth | Jetstream + Fortify |
| Queue | Database driver |
| Testing | PHPUnit 11 |

## Requisitos

- PHP 8.3+
- Composer 2+
- MySQL 8.0+
- Node.js 18+ / npm
- Extensiones PHP: mbstring, intl, pdo_mysql, zip, gd

---

## Instalación

### Opción 1: Local (sin deploy.sh)

Ideal para desarrollo. Requiere PHP, Composer y MySQL instalados localmente.

```bash
# 1. Clonar repositorio
git clone https://github.com/intersys-laravel/intersys_laravel.git
cd intersys_laravel

# 2. Instalar dependencias PHP
composer install

# 3. Instalar dependencias Node
npm install

# 4. Configurar entorno
cp .env.example .env
php artisan key:generate

# 5. Configurar base de datos en .env
# Editar: DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD

# 6. Ejecutar migraciones y seeders
php artisan migrate --seed

# 7. Crear symlink de storage (importante para imágenes y archivos)
php artisan storage:link
# Windows (PowerShell):
# Remove-Item public/storage -ErrorAction SilentlyContinue
# New-Item -ItemType Junction -Path public/storage -Target storage/app/public

# 8. Compilar assets
npm run build

# 9. Levantar servidor
php artisan serve
```

La aplicación estará disponible en **http://localhost:8000**

> **Primer usuario**: ejecutá `php artisan tinker` y creá un admin:
> ```php
> \App\Models\User::create([
>     'name' => 'Admin',
>     'email' => 'admin@intersys.com',
>     'password' => bcrypt('password'),
>     'role' => 'admin',
>     'email_verified_at' => now(),
> ]);
> ```

### Opción 2: Docker (recomendado)

Levanta todo el stack (app, MySQL, queue, nginx) sin instalar nada localmente.

```bash
# 1. Clonar y configurar
git clone https://github.com/intersys-laravel/intersys_laravel.git
cd intersys_laravel
cp .env.example .env

# 2. Ajustar .env para Docker
# DB_HOST=mysql  (por defecto en docker-compose.yml)
# APP_PORT=5005

# 3. Levantar contenedores
docker compose up -d

# 4. El setup automático ejecuta migraciones, seeders y storage:link
#    Esperá a que termine el servicio 'setup' antes de acceder

# La aplicación estará en http://localhost:5005
```

**Comandos útiles:**

```bash
# Ver logs en tiempo real
docker compose logs -f app

# Ejecutar comandos artisan dentro del contenedor
docker compose exec app php artisan <comando>

# Detener todo
docker compose down

# Detener y eliminar volúmenes (borra la DB)
docker compose down -v

# Reconstruir contenedores (después de cambiar dependencias)
docker compose up -d --build
```

**Docker Compose profiles:**

| Archivo | Uso |
|---------|-----|
| `docker-compose.yml` | Desarrollo (por defecto) |
| `docker-compose.dev.yml` | Desarrollo con hot-reload |
| `docker-compose.prod.yml` | Producción (sin volumes bind) |

```bash
# Usar un profile específico
docker compose -f docker-compose.prod.yml up -d
```

### Opción 3: Deploy en servidor (deploy.sh)

Script automatizado para producción. Hace backup, pull, migraciones, caches y verificación.

```bash
# 1. En el servidor, asegurar que el repositorio está en la carpeta correcta
cd /var/www/intersys_laravel

# 2. Hacelo ejecutable
chmod +x deploy.sh

# 3. Ejecutar dry-run (simula sin escribir nada)
./deploy.sh --dry-run

# 4. Deploy real
./deploy.sh
```

**Qué hace el script:**

1. ✅ Pre-flight checks (git limpio, branch correcta)
2. ✅ Backup automático: DB + storage + `.env` (mantiene últimos 5)
3. ✅ `git pull origin main`
4. ✅ `composer install --no-dev --optimize-autoloader`
5. ✅ `php artisan migrate --force`
6. ✅ Repair/crea el storage link (`public/storage` → `storage/app/public`)
7. ✅ Caches: config, routes, views, events
8. ✅ Permisos en `storage/` y `bootstrap/cache/`
9. ✅ Health check: verifica `/health` y revisa logs

**Rollback:**

```bash
./deploy.sh --rollback
```

**Variables de entorno del script:**

| Variable | Default | Descripción |
|----------|---------|-------------|
| `APP_DIR` | `/var/www/intersys_laravel` | Ruta del proyecto |
| `BACKUP_DIR` | `/backups` | Directorio de backups |
| `DB_NAME` | `intersys_laravel` | Base de datos MySQL |
| `DB_USER` | `root` | Usuario MySQL para backup |
| `WEB_USER` | `www-data` | Usuario del web server |
| `BRANCH` | `main` | Branch para desplegar |
| `APP_URL` | `http://localhost` | URL para health check |

```bash
# Ejemplo con variables personalizadas
APP_URL="https://intersys.ejemplo.com" WEB_USER="nginx" ./deploy.sh
```

---

## Configuración

### Variables `.env` críticas para producción

| Variable | Valor recomendado |
|----------|-------------------|
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `CACHE_DRIVER` | `redis` o `file` |
| `SESSION_DRIVER` | `database` o `redis` |
| `MAIL_MAILER` | `smtp` (si usás notificaciones) |

> ⚠️ **Nunca** dejés `APP_DEBUG=true` en producción.

### Storage Link

Para que las imágenes de clientes y vouchers sean accesibles vía web:

```bash
# Linux / macOS
php artisan storage:link

# Windows (si artisan falla)
Remove-Item public/storage -ErrorAction SilentlyContinue
New-Item -ItemType Junction -Path public/storage -Target storage/app/public
```

Esto crea un enlace `public/storage` → `storage/app/public`, permitiendo acceder a archivos en `/storage/clients/` y `/storage/vouchers/`.

---

## Testing

```bash
# Ejecutar toda la suite
php artisan test

# Con coverage
vendor/bin/phpunit --coverage-clover coverage.xml

# Filtrar por tipo
php artisan test --filter=PaymentFlowTest

# Solo tests de Feature
php artisan test --testsuite=Feature
```

## Code Quality

```bash
# Lint (verificar)
vendor/bin/pint --test

# Lint (auto-fix)
vendor/bin/pint

# Static Analysis
vendor/bin/phpstan analyse
```

## Roles y Permisos

| Rol | Ver | Crear | Editar | Eliminar |
|-----|-----|-------|--------|----------|
| **admin** | ✅ | ✅ | ✅ | ✅ |
| **operator** | ✅ | ✅ | ✅ | ❌ |
| **auditor** | ✅ | ❌ | ❌ | ❌ |

## Health Check

Endpoint público para monitoreo (no requiere autenticación):

```bash
curl http://localhost:8000/health
```

Respuesta esperada:
```json
{
    "status": "healthy",
    "checks": {
        "database": { "status": "ok" },
        "disk_space": { "status": "ok", "free_gb": 45.2 }
    },
    "timestamp": "2026-11-11T02:00:00Z"
}
```

> Configuralo en tu monitor externo (UptimeRobot, Hetzner, etc.) cada 5 min.

## Contribuir

1. Crear una rama (`feature/nombre` o `fix/nombre`)
2. Hacer commits con mensajes descriptivos (Conventional Commits)
3. Abrir un Pull Request a `main`
4. Esperar que los checks de CI pasen

## License

[MIT](LICENSE)