#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "$0")" && pwd)"
RUNTIME="$ROOT/.runtime"
if [ ! -f "$RUNTIME/artisan" ]; then
  rm -rf "$RUNTIME"
  composer create-project laravel/laravel:^9.0 "$RUNTIME" --no-interaction
fi
cp "$ROOT/local-demo/routes/web.php" "$RUNTIME/routes/web.php"
mkdir -p "$RUNTIME/app/Http/Controllers" "$RUNTIME/database/migrations" "$RUNTIME/database/seeders" "$RUNTIME/tests/Feature"
cp "$ROOT/local-demo/app/Http/Controllers/DemoCasinoController.php" "$RUNTIME/app/Http/Controllers/"
cp "$ROOT/local-demo/database/migrations/2023_01_01_000001_create_demo_casino_tables.php" "$RUNTIME/database/migrations/"
cp "$ROOT/local-demo/database/seeders/DatabaseSeeder.php" "$RUNTIME/database/seeders/"
cp "$ROOT/local-demo/tests/Feature/DemoCasinoTest.php" "$RUNTIME/tests/Feature/"
cp "$ROOT/local-demo/.env.example" "$RUNTIME/.env"
cd "$RUNTIME"
php artisan key:generate --force
php artisan migrate:fresh --seed --force
php artisan optimize:clear
php artisan serve --host=0.0.0.0 --port=8000
