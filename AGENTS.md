# Repository Instructions

Laravel 12 application using PHP 8.3 locally, Livewire 4, Flux UI, Tailwind CSS 4, Pest 4, and Vite.

## Commands

- Install/setup: `composer run setup` (installs dependencies, creates `.env`, generates key, migrates, installs npm packages, builds assets).
- Development: `composer run dev` starts Laravel, queue listener, and Vite together.
- Production asset build: `npm run build`; frontend HMR only: `npm run dev`.
- Format PHP: `vendor/bin/pint --dirty` or `composer lint`.
- Run all tests: `php artisan test --compact`.
- Run one file: `php artisan test --compact tests/Feature/Rekening/CustomerCrudTest.php`.
- Run one test: `php artisan test --compact --filter=CustomerCrud`.
- `composer run test` clears config, checks Pint, then runs the full suite.

Run Pint before tests when PHP files change; run `npm run build` when frontend assets change. Tests force SQLite in-memory via `phpunit.xml`, while normal development/production database settings come from `.env`.

## Application boundaries

- Admin routes use the `web` guard and live under `/rekening`; most screens are full-page Livewire components in `app/Livewire/Rekening/` with views in `resources/views/livewire/rekening/`.
- Agent routes use the separate `agent` guard and `/agent` prefix; agent screens are in `app/Livewire/Agent/`.
- Guest redirect selection is prefix-based in `bootstrap/app.php`; check it before changing authentication or adding guards.
- Admin role/permission/backup management requires the `Super Admin` role in `routes/web.php`.
- API authentication is Sanctum-based in `routes/api.php`.
- Observers in `app/Observers/` create agent notifications for account, card, complaint, and shipment changes; extend that pattern instead of creating notifications inside UI actions.
- Shipment tracking is isolated in `app/Services/KlikResiService.php`; regional customer fields use an external Indonesian administrative API rather than local region tables.
- Exports are implemented by `app/Exports/` classes and invoked from the relevant Livewire CRUD components; PDF templates are in `resources/views/exports/`.

## Non-obvious conventions

- Internal identifiers are English; user-facing labels, validation messages, notifications, and flash messages are Indonesian.
- Reuse existing Flux UI components and sibling Livewire CRUD patterns; preserve responsive and dark-mode behavior.
- Validate Livewire actions server-side and eager-load relationships to avoid N+1 queries.
- Do not use floating-point arithmetic for money; use integer units or BCMath-compatible handling.
- Domain models use UUID primary keys via `HasUuids`; treat customer, account, card, and credential-related data as sensitive.
- Do not add dependencies or top-level directories without explicit approval.

## References

Read `.ai/guidelines/rekening.md` for project rules and `.ai/skills/laravel-rekening/SKILL.md` for domain-specific workflow. `CLAUDE.md` contains additional verified architecture notes, but avoid duplicating its generated Laravel Boost guidance here.
