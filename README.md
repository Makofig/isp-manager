# ISP Manager

Panel de gestión para proveedores de internet, construido con [Laravel](https://laravel.com) + Tailwind CSS (Vite).

## Requisitos

- PHP 8.2+ y Composer (para desarrollo local)
- PostgreSQL 14+ corriendo localmente (la app apunta a `127.0.0.1:5432`, base `intersys_laravel`)
- Node.js 20+ (para los assets)
- Docker + Docker Compose (si usás el despliegue contenedorizado)

## Despliegue local

> La app usa la base de datos local (ver `.env`), así que Postgres debe estar corriendo antes de migrar.

Abrí **dos terminales** en la raíz del proyecto:

**Terminal 1 — backend:**

```bash
# 1. Dependencias
composer install
npm install

# 2. APP_KEY (solo la primera vez; el .env viene sin key)
php artisan key:generate

# 3. Migrar + seed
php artisan migrate --seed
# → si la base viene sucia o con tablas viejas:
#   php artisan migrate:fresh --seed

# 4. Queue worker (la app tiene jobs en cola: sesiones/cache/cola usan database)
php artisan queue:work

# 5. Servidor de la app (en el mismo terminal, con Ctrl+Z o en otra pestaña)
php artisan serve
```

**Terminal 2 — assets (hot reload):**

```bash
npm run dev
```

**Verificación:**

- [ ] `http://localhost` responde (o la URL de `APP_URL` en `.env`)
- [ ] Ingresás con `admin@admin.com` / `admin123456`
- [ ] El terminal con `queue:work` se queda corriendo sin errores

## Despliegue con Docker

Todo queda definido en `docker-compose.yml` (app + nginx + mysql + queue worker). No necesitás PHP ni Node instalado.

```bash
# 1. Levantar todo
docker compose up -d --build

# 2. Verificar que los servicios estén arriba
docker compose ps

# 3. Revisar logs de la app
docker compose logs -f app

# 4. Una vez que `app` responde, correr migraciones + seed
#    dentro del contenedor PHP (tiene PHP, Composer y las extensiones)
docker compose exec app php artisan migrate --seed
```

| Servicio | Puerto | Detalle |
|----------|--------|---------|
| nginx | `5005` | Front de la app → `http://localhost:5005` |
| mysql | `3309` | Base `intersys` expuesta para debugging |

El comando del contenedor `app`, al arrancar, ejecuta:

```bash
php artisan key:generate --force &&
php artisan config:clear &&
php artisan cache:clear &&
php-fpm
```

Y el servicio `queue` corre el worker por nosotros:

```bash
php artisan queue:work --sleep=3 --tries=3 --timeout=120
```

**Notas importantes del setup Docker:**

- Las migraciones **no se corren automáticamente** en el contenedor `app`: hay que ejecutarlas manualmente con `docker compose exec app php artisan migrate --seed` cada vez que arrancás con una base vacía o cambiás el schema.
- El `.env` del host se monta tal cual en el contenedor (volumen `.`); asegurate de que `DB_*` sea consistente con el servicio de BD (`DB_HOST=mysql`, usuario `admin`, password `admin123456`, base `intersys`).
- `APP_KEY`: el `key:generate --force` sobreescribe la key del `.env` en cada arranque. Con el volumen `.` montado es aceptable en dev, pero generá una key fija si la querés estable entre reinicios.

## Cuentas por defecto

| Rol | Email | Password |
|-----|-------|----------|
| Admin | `admin@admin.com` | `admin123456` |
| Test | `test@example.com` | `password` |

Los defaults se pueden sobreescribir vía `.env` (`ADMIN_EMAIL`, `ADMIN_NAME`, `ADMIN_PASSWORD`). El seeder es idempotente: re-correrlo no duplica usuarios.

## Colas (resumen)

| Entorno | Cómo corre el worker |
|---------|----------------------|
| Local | `php artisan queue:work` en su propia terminal (no detener) |
| Docker | Servicio `queue` de `docker-compose.yml` (arranca solo con `up`) |

## Estructura relevante

- `app/` — código PHP (models, controllers, services)
- `database/migrations/` — schema (users, proveedores, planes, cuotas, pagos, clientes, etc.)
- `database/seeders/` — `AdminUserSeeder` + `DatabaseSeeder`
- `resources/` — vistas Blade + assets (Tailwind)
- `docker/` — Dockerfile PHP y config de nginx

## Security Vulnerabilities

If you discover a security vulnerability within this project, please open a private issue. For the framework itself, refer to the [Laravel security policy](https://laravel.com/docs/security).

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
