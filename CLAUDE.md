# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

- **Build assets:** `npm run build` (production) / `npm run dev` (HMR)
- **Run all tests:** `php artisan test --compact`
- **Run one test file:** `php artisan test --compact tests/Feature/Rekening/CustomerCrudTest.php`
- **Run one test by name:** `php artisan test --compact --filter=test_name`
- **Lint:** `vendor/bin/pint --dirty`
- **Dev server (all-in-one):** `composer run dev` — starts Laravel, queue worker, and Vite together
- **Dev server (manual):** `php artisan serve` + `php artisan queue:listen --tries=1` + `npm run dev`

## Architecture

### Dual authentication guards

Two separate user types with independent auth, configured in `config/auth.php`:

- **`web` guard → `User` model** — Admin panel, protected by Spatie Laravel Permission. Routes live under `auth` + `verified` middleware. Only `Super Admin` role can access users/roles/permissions/backups management.
- **`agent` guard → `Agent` model** — Agent portal with a separate login at `/agent/login`. Routes under `auth:agent`. Agents see only their own dashboard, help desk, and shipments.

Guest/user redirect logic is in `bootstrap/app.php` and dispatches by URL prefix (`agent*` → agent guard, everything else → web guard). Do not add a new guard or auth path without checking this dispatch first.

### Livewire full-page components

Each screen is a single Livewire component with both logic and view — no separate controllers for CRUD pages.

- Admin screens: `App\Livewire\Rekening\*Crud` (CustomerCrud, AccountCrud, etc.)
- Agent screens: `App\Livewire\Agent\*` (Dashboard, HelpDesk, Shipment)
- Each full-page component uses `#[Layout('layouts.app.sidebar')]` and `#[Title('...')]` attributes at the class level
- CRUD pattern: `WithPagination` + `WithFileUploads`, `$search` property, `$sortField`/`$sortDirection`, `$perPage`, modal-based create/edit with `$showModal`, `$editId` to distinguish modes
- Routes map Livewire classes directly: `Route::get('/customers', CustomerCrud::class)`

### View structure

- `resources/views/livewire/rekening/` — admin Livewire views (one per `Rekening\*Crud` component)
- `resources/views/livewire/agent/` — agent Livewire views
- `resources/views/components/rekening/` — reusable admin Blade components
- `resources/views/components/agent/` — reusable agent Blade components
- `resources/views/layouts/` — `app.sidebar` (admin), `auth.*` (login/register shells)
- `resources/views/exports/` — PDF and Excel export templates
- UI library is **Flux UI** (free edition) — prefer existing Flux components (`flux:button`, `flux:modal`, `flux:input`, `flux:table`, `flux:select`) over custom markup

### Observers → Agent notifications

`AccountObserver`, `CardObserver`, `ComplaintObserver`, and `ShipmentObserver` create `AgentNotification` records when domain models are created or updated. The agent portal's `NotificationBell` component reads these. When adding new model events that should notify agents, follow the existing observer pattern — don't inline notification creation in controllers or Livewire actions.

### Exports

- Excel: `App\Exports\*Export` classes via Maatwebsite Laravel Excel
- PDF: Barryvdh DOMPDF, with print-friendly templates in `resources/views/exports/`
- Export actions live inside the relevant `*Crud` Livewire component (no separate export controller)

### Regional data (Indonesian administrative hierarchy)

Customer forms load provinces → regencies → districts → villages from an external Indonesian API (`Http::get(...)` in `CustomerCrud`). There is no local region table. The selection is cascading — picking a province fetches its regencies, etc. If this API is down, customer creation is blocked; be aware of this when debugging regional dropdowns.

### Shipments

`KlikResiService` in `app/Services/` integrates with the KlikResi expedition API for tracking delivery of account documents. Shipment status updates flow through `ShipmentCrud` and the agent portal's Shipment screen.

## Domain conventions

- **Money:** never use float arithmetic for financial values — use integers (satuan) or `bcmath`
- **Card data:** CVV/PIN are intentionally not separate columns; sensitive values go in the `notes` text field
- **UUIDs:** all domain tables (customers, accounts, cards, agents, complaints, shipments) use UUID primary keys via `HasUuids`
- **Language:** class/method/property names in English; all user-facing strings (labels, validation messages, notifications, flash messages) in Indonesian
- **Spatie roles:** admin access is gated by role — check `middleware(['role:Super Admin'])` on routes before adding admin-only features

## Project-specific references

Detailed conventions live in `.ai/` — read these when working on domain features:

- `.ai/guidelines/rekening.md` — core project conventions, Laravel expectations, UI rules
- `.ai/skills/laravel-rekening/SKILL.md` — domain skill for Rekening feature work
