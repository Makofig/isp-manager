# Security Audit — Medium Priority Fixes

**Date:** October 30, 2026  
**Branch:** `main` (continuing)  
**Strategy:** inline  
**Runner:** `php artisan test`

## Specs

S16: Fix Dashboard cache key typo `'stast_global'` → `'stats_global'` (M2)
S17: Validate actual MIME type of uploaded files in clientController (2.6)
S18: Replace raw role strings with User model constants in all Policies (M4)
S19: Optimize Statistics Livewire queries with eager loading + cache (2.3)

## Tasks

- [x] T13: Fix Dashboard cache key typo — `stast` → `stats` (inline) — S16 — commit `fdf38b4`
- [x] T14: Validate MIME type of uploaded files (inline) — S17 — commit `fdf38b4`
- [x] T15: Add role constants to Policies (inline) — S18 — commit `fdf38b4`
- [x] T16: Optimize Statistics queries with cache + consolidated selectRaw (inline) — S19 — commit `fdf38b4`

## Log

L1: User requested closing all Medium priority findings from audit.
L2: [2026-10-30] ALL 4 tasks COMPLETE. Commit: fdf38b4. Dashboard cache typo fixed. MIME validation added to uploads. 5 Policies now use User::ROLE_* constants. Statistics optimized from ~9 queries to 4 + 30min cache. Tests verified: 0 regressions.
