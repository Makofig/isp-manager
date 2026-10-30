# Security Audit Phase 1 — Implementation

**Date:** October 30, 2026  
**Branch:** `security-phase1`  
**Strategy:** `auto-chain` (commits ~400 lines across multiple files)  
**Runner:** `php artisan test`

## Specs

S1: Disable self-registration in Fortify or change default role to `'operator'`
S2: Enable `Features::emailVerification()` in Fortify config
S3: Remove `estado` from Payments `$fillable` to prevent mass assignment
S4: Add `$this->authorize()` to `QuotaController::store()` and wire `QuotaPolicy` in AuthServiceProvider
S5: Add `$this->authorize('ban', $client)` to `toggleBan()` in Livewire ClientsTable
S6: Remove or guard the test user in DatabaseSeeder for production
S7: Add security headers (X-Frame-Options, X-Content-Type-Options, X-XSS-Protection, CSP, HSTS, Referrer-Policy) to Nginx config
S8: Filter admin notifications by role in `RemindOverduePayments` and `GenerateQuotaPayments`
S9: Set Sanctum token expiration to 30 days
S10: Change migration default role from `'admin'` to `'operator'`

## Tasks

- [ ] T1: Change default role to `operator` — migration + fortify config (inline) — S1, S10
- [ ] T2: Enable email verification — fortify config (inline) — S2
- [ ] T3: Remove `estado` from Payments `$fillable` (inline) — S3
- [ ] T4: Add authorization to QuotaController + wire QuotaPolicy in AuthServiceProvider + add ban auth to ClientsTable (inline) — S4, S5
- [ ] T5: Guard test user in DatabaseSeeder for production (inline) — S6
- [ ] T6: Add security headers to Nginx (inline) — S7
- [ ] T7: Filter admin notifications by role in commands/jobs (inline) — S8
- [ ] T8: Set Sanctum token expiration (inline) — S9

## Log

L1: User requested implementing Phase 1 security fixes from audit (docs/AUDIT.md). Each task should be marked as completed with evidence.
