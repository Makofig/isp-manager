# InterSys — Network Management Platform

[![CI](https://github.com/intersys-laravel/intersys_laravel/actions/workflows/ci.yml/badge.svg)](https://github.com/intersys-laravel/intersys_laravel/actions/workflows/ci.yml)
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

## Instalación

```bash
# Clonar repositorio
git clone https://github.com/intersys-laravel/intersys_laravel.git
cd intersys_laravel

# Instalar dependencias PHP
composer install

# Instalar dependencias Node
npm install

# Configurar entorno
cp .env.example .env
php artisan key:generate

# Configurar base de datos en .env
# DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD

# Ejecutar migraciones y seeders
php artisan migrate --seed

# Compilar assets
npm run build

# Levantar servidor
php artisan serve
```

## Docker

```bash
# Levantar contenedores
docker compose up -d

# La aplicación estará en http://localhost:5005
```

## Tests

```bash
# Ejecutar toda la suite
php artisan test

# Con coverage
vendor/bin/phpunit --coverage-clover coverage.xml

# Filtrar por tipo
php artisan test --filter=NotificationsTest
```

## Code Quality

```bash
# Lint (verificar)
vendor/bin/pint --test

# Lint (fix)
vendor/bin/pint

# Static Analysis
vendor/bin/phpstan analyse
```

## Estructura del Proyecto

```
app/
├── Console/Commands/     # Comandos artisan (generate-monthly-quotas, remind-overdue)
├── Http/Controllers/     # Controllers
├── Jobs/                 # Jobs (GenerateQuotaPayments)
├── Livewire/             # Componentes Livewire
├── Mail/                 # Mailable classes
├── Models/               # Eloquent models
├── Notifications/        # Notification classes
├── Policies/             # Authorization policies
├── Providers/            # Service providers
├── Services/             # Business logic services
└── ...

resources/
├── css/app.css           # Estilos dark theme
├── js/                   # Alpine.js components
└── views/                # Blade templates
    ├── components/       # Componentes reutilizables
    ├── layouts/          # Layouts (app, guest)
    ├── livewire/         # Vistas Livewire
    └── profile/          # Vistas de perfil
```

## Roles y Permisos

| Rol | Ver | Crear | Editar | Eliminar |
|-----|-----|-------|--------|----------|
| **admin** | ✅ | ✅ | ✅ | ✅ |
| **operator** | ✅ | ✅ | ✅ | ❌ |
| **auditor** | ✅ | ❌ | ❌ | ❌ |

## CI/CD

Los workflows de GitHub Actions ejecutan automáticamente:

- **Lint**: Laravel Pint (code style)
- **Static Analysis**: PHPStan level 5
- **Tests**: PHPUnit con coverage

Ver [.github/workflows](.github/workflows) para más detalles.

## Contribuir

1. Crear una rama (`feature/nombre` o `fix/nombre`)
2. Hacer commits con mensajes descriptivos
3. Abrir un Pull Request a `develop`
4. Esperar que los checks de CI pasen

## License

[MIT](LICENSE)
