# Security Audit Phase 2 — Architecture Improvements

**Date:** October 30, 2026
**Branch:** `security-phase1` (continuing)
**Strategy:** `auto-chain`
**Runner:** `php artisan test`

## Specs

S11: Fix quota `numero` unique constraint — add `year` column, change unique to `(numero, year)` so quotas can be generated for future years (H4)
S12: Add database indexes on frequently queried columns: `pagos.created_at`, `pagos.estado`, `cliente.id_plan` (M8)
S13: Use UUIDs for uploaded file names instead of `time()` (H5)
S14: Add composite indexes for statistics queries: `pagos.fecha_pago`, `pagos.id_cliente` (M1)
S15: Fix soft-delete cascade inconsistency on payments referencing deleted clients (H6)

## Tasks

- [x] T9: Fix quota unique constraint — add `year` column + composite unique (migration + service + model + controller) — S11
- [x] T10: Add database indexes for performance (migration) — S12, S14
- [x] T11: Use UUIDs for file uploads instead of `time()` (controller + Livewire) — S13
- [x] T12: Fix soft-delete cascade on payments referencing deleted clients (migration + model) — S15

## Log

L1: User requested implementing Phase 2 architecture fixes from audit (docs/AUDIT.md). Phase 1 already complete.
