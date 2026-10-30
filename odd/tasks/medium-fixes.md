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

- [ ] T13: Fix Dashboard cache key typo — `stast` → `stats` (inline) — S16
- [ ] T14: Validate MIME type of uploaded files (inline) — S17
- [ ] T15: Add role constants to User model and update Policies (inline) — S18
- [ ] T16: Optimize Statistics queries (inline) — S19

## Log

L1: User requested closing all Medium priority findings from audit.
