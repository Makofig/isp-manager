#!/usr/bin/env bash
# =============================================================================
# ISP Management — Production Deploy Script
# Usage: ./deploy.sh [--dry-run] [--rollback]
#
# Requirements: git, composer, mysql, php (artisan accessible)
# =============================================================================
set -euo pipefail

# ─────────────────────────────────────────────────────────────────────────────
# Configuration — adjust these for your server
# ─────────────────────────────────────────────────────────────────────────────
APP_DIR="${APP_DIR:-/var/www/intersys_laravel}"
BACKUP_DIR="${BACKUP_DIR:-/backups}"
DB_NAME="${DB_NAME:-intersys_laravel}"
DB_USER="${DB_USER:-root}"
WEB_USER="${WEB_USER:-www-data}"
BRANCH="${BRANCH:-main}"
TAGS_BRANCH="${TAGS_BRANCH:-deploy}"  # branch for git-tag markers

# ─────────────────────────────────────────────────────────────────────────────
# Helper functions
# ─────────────────────────────────────────────────────────────────────────────
timestamp() { echo "[$(date '+%Y-%m-%d %H:%M:%S')] $*"; }
step()      { timestamp ">> $*"; }
warn()      { timestamp "[WARN] $*"; }
err()       { timestamp "[ERROR] $*" >&2; }
ok()        { timestamp "[OK] $*"; }

# Exit codes
readonly EXIT_OK=0
readonly EXIT_ABORT=1
readonly EXIT_ROLLBACK_DONE=2

# Flags
DRY_RUN=false
ROLLBACK=false
PREVIOUS_REF=""

for arg in "$@"; do
    case "$arg" in
        --dry-run)  DRY_RUN=true ;;
        --rollback) ROLLBACK=true ;;
        *)          err "Unknown option: $arg"; exit $EXIT_ABORT ;;
    esac
done

# Ensure we're not running as root directly (safety)
if [[ $EUID -eq 0 ]]; then
    err "Do not run this script as root. Use 'sudo -u <deploy-user> ./deploy.sh'"
    exit $EXIT_ABORT
fi

# ─────────────────────────────────────────────────────────────────────────────
# ROLLBACK MODE
# ─────────────────────────────────────────────────────────────────────────────
if $ROLLBACK; then
    step "ROLLBACK MODE — reverting to safety"

    git -C "$APP_DIR" log --oneline -3

    if [[ ! -f "$APP_DIR/.git/previous_ref" ]]; then
        err "No previous_ref found. Manual rollback needed."
        exit $EXIT_ABORT
    fi

    PREVIOUS_REF=$(cat "$APP_DIR/.git/previous_ref")
    step "Rolling back to: $PREVIOUS_REF"

    if $DRY_RUN; then
        warn "[DRY-RUN] Would run: git -C $APP_DIR reset --hard $PREVIOUS_REF"
        warn "[DRY-RUN] Would rebuild caches"
        exit $EXIT_OK
    fi

    # Backup current state before rollback
    DB_BACKUP="$BACKUP_DIR/pre_rollback_db_$(date +%Y%m%d_%H%M%S).sql.gz"
    mysqldump -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" 2>/dev/null | gzip > "$DB_BACKUP" || true

    git -C "$APP_DIR" reset --hard "$PREVIOUS_REF"
    cd "$APP_DIR"
    composer install --no-dev --optimize-autoloader --quiet
    php artisan optimize:clear
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    php artisan event:cache

    chown -R "$WEB_USER":"$WEB_USER" "$APP_DIR/storage" \
                       "$APP_DIR/bootstrap/cache" 2>/dev/null || true

    ok "Rollback to $PREVIOUS_REF complete"
    exit $EXIT_ROLLBACK_DONE
fi

# ─────────────────────────────────────────────────────────────────────────────
# PRE-FLIGHT CHECKS
# ─────────────────────────────────────────────────────────────────────────────
step "PRE-FLIGHT CHECKS"

# 1. App directory exists and is a git repo
if [[ ! -d "$APP_DIR/.git" ]]; then
    err "$APP_DIR is not a git repository"
    exit $EXIT_ABORT
fi

# 2. Clean working tree (staged + unstaged)
UNCOMMITTED=$(git -C "$APP_DIR" status --porcelain)
if [[ -n "$UNCOMMITTED" ]]; then
    err "Uncommitted changes detected in $APP_DIR:"
    echo "$UNCOMMITTED"
    exit $EXIT_ABORT
fi

# 3. Check correct branch
CURRENT_BRANCH=$(git -C "$APP_DIR" rev-parse --abbrev-ref HEAD)
if [[ "$CURRENT_BRANCH" != "$BRANCH" ]]; then
    warn "Current branch is '$CURRENT_BRANCH', expected '$BRANCH'. Switching..."
    git -C "$APP_DIR" checkout "$BRANCH"
fi

# Dry-run early exit
if $DRY_RUN; then
    warn "[DRY-RUN] No changes will be made. Exiting."
    exit $EXIT_OK
fi

# ─────────────────────────────────────────────────────────────────────────────
# STEP 1: SAVE PREVIOUS REF (for rollback)
# ─────────────────────────────────────────────────────────────────────────────
step "Saving previous ref for rollback"
git -C "$APP_DIR" rev-parse HEAD > "$APP_DIR/.git/previous_ref"
PREVIOUS_REF=$(cat "$APP_DIR/.git/previous_ref")
ok "Previous ref: $PREVIOUS_REF"

# ─────────────────────────────────────────────────────────────────────────────
# STEP 2: BACKUP PRODUCTION STATE
# ─────────────────────────────────────────────────────────────────────────────
step "BACKUP — database + storage + .env"
BACKUP_TS="$(date +%Y%m%d_%H%M%S)"
mkdir -p "$BACKUP_DIR"

# DB backup
DB_BACKUP="$BACKUP_DIR/db_backup_${BACKUP_TS}.sql.gz"
mysqldump -u "$DB_USER" "$DB_NAME" 2>/dev/null | gzip > "$DB_BACKUP" && \
    ok "DB backup: $DB_BACKUP" || \
    warn "DB backup failed — check credentials"

# Storage backup (vouchers, photos, etc.)
if [[ -d "$APP_DIR/storage/app/public" ]]; then
    STORAGE_BACKUP="$BACKUP_DIR/storage_backup_${BACKUP_TS}.tar.gz"
    tar -czf "$STORAGE_BACKUP" -C "$APP_DIR/storage/app" "public" 2>/dev/null && \
        ok "Storage backup: $STORAGE_BACKUP" || \
        warn "Storage backup failed"
fi

# .env backup
if [[ -f "$APP_DIR/.env" ]]; then
    cp "$APP_DIR/.env" "$BACKUP_DIR/.env.backup.${BACKUP_TS}" && \
        ok ".env backup done"
fi

# Retain last 5 backups only (cleanup old ones)
find "$BACKUP_DIR" -name "db_backup_*.sql.gz" -type f | sort -r | tail -n +6 | xargs rm -f 2>/dev/null || true
find "$BACKUP_DIR" -name "storage_backup_*.tar.gz" -type f | sort -r | tail -n +6 | xargs rm -f 2>/dev/null || true
find "$BACKUP_DIR" -name ".env.backup.*" -type f | sort -r | tail -n +6 | xargs rm -f 2>/dev/null || true

# ─────────────────────────────────────────────────────────────────────────────
# STEP 3: PULL NEW CODE
# ─────────────────────────────────────────────────────────────────────────────
step "PULL — origin $BRANCH"
cd "$APP_DIR"
git pull origin "$BRANCH" && ok "Pull successful" || {
    warn "Pull failed. This may be normal if already up-to-date."
    git -C "$APP_DIR" status --short | head -5
}

NEW_REF=$(git -C "$APP_DIR" rev-parse --short HEAD)
ok "Deploying commit: $NEW_REF"

# ─────────────────────────────────────────────────────────────────────────────
# STEP 4: PHP DEPENDENCIES
# ─────────────────────────────────────────────────────────────────────────────
step "PHP — composer install (no-dev, optimized)"
if command -v composer &>/dev/null; then
    cd "$APP_DIR"
    composer install --no-dev --optimize-autoloader --quiet 2>/dev/null && \
        ok "Composer updated" || warn "Composer install had warnings (check manually)"
else
    err "composer not found. Aborting."
    exit $EXIT_ABORT
fi

# ─────────────────────────────────────────────────────────────────────────────
# STEP 5: MIGRATIONS (safe — skips if no pending)
# ─────────────────────────────────────────────────────────────────────────────
step "Migrations"
cd "$APP_DIR"
PENDING=$(php artisan migrate:status 2>/dev/null | grep -c 'Pending\|Outstanding' || true)
if [[ "$PENDING" -gt 0 ]]; then
    php artisan migrate --force && ok "Migrations applied" || {
        err "Migrations failed! Initiating manual check..."
        php artisan migrate:status
        exit $EXIT_ABORT
    }
else
    ok "No pending migrations"
fi

# ─────────────────────────────────────────────────────────────────────────────
# STEP 6: REPAIR STORAGE LINK
# ─────────────────────────────────────────────────────────────────────────────
step "Storage link"
if [[ -L "$APP_DIR/public/storage" ]] || [[ -d "$APP_DIR/public/storage" ]]; then
    # Check if it's a valid symlink pointing to the right target
    ACTUAL_TARGET=$(readlink -f "$APP_DIR/public/storage" 2>/dev/null || true)
    EXPECTED_TARGET=$(readlink -f "$APP_DIR/storage/app/public" 2>/dev/null || true)
    if [[ -n "$ACTUAL_TARGET" && -n "$EXPECTED_TARGET" && "$ACTUAL_TARGET" != "$EXPECTED_TARGET" ]]; then
        warn "Storage link points to wrong location. Repairing..."
        rm -rf "$APP_DIR/public/storage"
    elif [[ -z "$ACTUAL_TARGET" ]]; then
        warn "Storage link is broken. Repairing..."
        rm -rf "$APP_DIR/public/storage"
    else
        ok "Storage link is valid"
    fi
fi

if [[ ! -d "$APP_DIR/public/storage" ]]; then
    php artisan storage:link 2>/dev/null && ok "Storage link created" || {
        # Fallback: create directly if artisan fails
        ln -sfn "$APP_DIR/storage/app/public" "$APP_DIR/public/storage" 2>/dev/null && \
            ok "Storage link created (fallback)" || \
            warn "Could not create storage link — check manually"
    }
else
    ok "Storage link already exists and is valid"
fi

# ─────────────────────────────────────────────────────────────────────────────
# STEP 7: OPTIMIZE FOR PRODUCTION
# ─────────────────────────────────────────────────────────────────────────────
step "Optimize — caches"
cd "$APP_DIR"
php artisan optimize:clear && ok "Old caches cleared"

php artisan config:cache    && ok "Config cached" || { err "Config cache failed"; exit $EXIT_ABORT; }
php artisan route:cache    && ok "Routes cached"  || { err "Route cache failed"; exit $EXIT_ABORT; }
php artisan view:cache     && ok "Views cached"   || { err "View cache failed"; exit $EXIT_ABORT; }
php artisan event:cache    && ok "Events cached"  || true  # not all apps have events

# ─────────────────────────────────────────────────────────────────────────────
# STEP 8: PERMISSIONS
# ─────────────────────────────────────────────────────────────────────────────
step "Permissions"
chown -R "$WEB_USER":"$WEB_USER" "$APP_DIR/storage" \
                     "$APP_DIR/bootstrap/cache" 2>/dev/null || \
    warn "Could not chown — checking web user"
chmod -R 775 "$APP_DIR/storage" \
             "$APP_DIR/bootstrap/cache" 2>/dev/null || true
ok "Permissions set"

# ─────────────────────────────────────────────────────────────────────────────
# STEP 9: POST-DEPLOY CHECKS
# ─────────────────────────────────────────────────────────────────────────────
step "POST-DEPLOY CHECKS"

# 8.1 App version + status
cd "$APP_DIR"
php artisan about 2>/dev/null | head -4 && ok "artisan responds" || \
    warn "artisan about had output to stderr"

# 8.2 Health endpoint
if command -v curl &>/dev/null; then
    APP_URL="${APP_URL:-http://localhost}"
    HEALTH=$(curl -sf --max-time 5 "${APP_URL}/health" 2>/dev/null || echo "unreachable")

    if echo "$HEALTH" | grep -q '"status"'; then
        DB_OK=$(echo "$HEALTH" | grep -o '"database":[^,}]*' || true)
        DISK_OK=$(echo "$HEALTH" | grep -o '"disk_space":[^,}]*' || true)
        ok "Health check: $DB_OK, $DISK_OK"
    else
        warn "Health endpoint unreachable via curl (may need correct APP_URL or HTTPS)"
    fi
fi

# 8.3 Recent log errors (only last 50 lines to avoid noise)
LOG_ERRORS=$(tail -50 "$APP_DIR/storage/logs/laravel.log" 2>/dev/null | \
             grep -ci 'error\|exception\|emergency' || true)
if [[ "$LOG_ERRORS" -gt 0 ]]; then
    warn "Found $LOG_ERRORS errors in recent logs — review: $APP_DIR/storage/logs/laravel.log"
else
    ok "Logs clean"
fi

# ─────────────────────────────────────────────────────────────────────────────
# DONE
# ─────────────────────────────────────────────────────────────────────────────
echo ""
ok "=========================================="
ok " Deploy complete!"
ok " Commit: $NEW_REF"
ok " Rollback ref: $PREVIOUS_REF"
ok " To rollback: ./deploy.sh --rollback"
ok "=========================================="
echo ""

exit $EXIT_OK
