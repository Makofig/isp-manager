# CI/CD — InterSys

Automatización de calidad de código y tests.

## Workflows

### `ci.yml` — Pipeline completo
Se ejecuta en cada push/PR a `main` y `develop`.

| Job | Descripción |
|-----|-------------|
| **lint** | Laravel Pint — verifica code style |
| **static-analysis** | PHPStan (level 5) — análisis estático |
| **tests** | PHPUnit — ejecuta toda la suite de tests |

### `lint.yml` — Solo code style
Verifica que el código siga los estándares definidos en `pint.json`.

### `tests.yml` — Solo tests
Ejecuta la suite completa de PHPUnit con coverage y sube a Codecov.

### `static-analysis.yml` — Solo PHPStan
Análisis estático de código nivel 5.

## Configuración

| Archivo | Propósito |
|---------|-----------|
| `pint.json` | Config de Laravel Pint (preset Laravel) |
| `phpstan.neon` | Config de PHPStan (level 5) |
| `.github/dependabot.yml` | Actualización automática de dependencias |
| `codecov.yml` | Config de cobertura de Codecov |

## Requisitos para CI

- PHP 8.3+
- MySQL 8.0
- Extensiones: mbstring, intl, pdo_mysql, zip, gd

## Ejecutar localmente

```bash
# Lint (dry-run)
vendor/bin/pint --test

# Fix lint
vendor/bin/pint

# Static analysis
vendor/bin/phpstan analyse

# Tests
php artisan test

# Tests con coverage
vendor/bin/phpunit --coverage-clover coverage.xml
```
