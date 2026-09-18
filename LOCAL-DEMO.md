# Local-only runtime

This branch adds a Laravel 9-compatible, local-only demo adapter around the repository's session/catalog concepts. It does not contact or proxy external casino providers and uses fake balances only.

Start everything:

```bash
docker compose up --build
```

URL: http://localhost:8000

Verify:

```bash
curl http://localhost:8000/health
curl http://localhost:8000/
curl -X POST http://localhost:8000/api/demo/sessions -H 'Content-Type: application/json' -d '{"game_id":"local-deterministic-slot","player_id":"trial-player","currency":"USD"}'
```

Use the returned token:

```bash
curl -X POST http://localhost:8000/api/demo/sessions/TOKEN/events -H 'Content-Type: application/json' -d '{"event":"init"}'
curl -X POST http://localhost:8000/api/demo/sessions/TOKEN/events -H 'Content-Type: application/json' -d '{"event":"spin","bet":100}'
```

Run tests inside the app container/runtime with `cd .runtime && php artisan test`.

The original `casino-dog` package remains in place for auditability. Its unavailable Nova support dependency and external-provider routes are intentionally not loaded by this local adapter.
