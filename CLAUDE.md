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

===

<laravel-boost-guidelines>
=== .ai/rekening rules ===

# Rekening Project Guidelines

This project is a Laravel 12 application that uses Livewire 4, Flux UI, Tailwind CSS 4, and Pest.

## Core conventions

- Follow existing project patterns in sibling files before introducing a new approach.
- Do not add new top-level folders or new dependencies without explicit approval.
- Prefer Laravel conventions and framework-native solutions over custom or raw implementations.
- Keep internal code identifiers in English.
- Keep labels, helper text, validation messages, and notifications in Indonesian unless the surrounding UI already uses English.

## Laravel expectations

- Use Laravel Boost documentation search before guessing Laravel ecosystem behavior.
- Prefer Eloquent relationships and eager loading over manual joins or raw queries.
- Use named routes and `route()` for internal links.
- Use Form Requests for controller validation.
- Validate Livewire actions on the server.
- Use config values instead of `env()` outside configuration files.
- Remember Laravel 12 uses the modern structure, including `bootstrap/app.php` for middleware and console-related setup.

## UI expectations

- Reuse existing Flux UI and Blade patterns before building custom UI.
- Preserve dark mode support and responsive behavior when editing views.
- Use Tailwind CSS 4 conventions and avoid deprecated utilities.
- Use `wire:loading`, `wire:dirty`, and `wire:key` where they improve UX and correctness.

## Testing and verification

- Add or update Pest tests for meaningful behavior changes.
- Run the smallest relevant test command with `php artisan test --compact`.
- Run `vendor/bin/pint --dirty` before finishing changes.
- If frontend changes are not visible, rebuild assets with `npm run build` or run the local dev process.

## Domain notes

- Treat financial and account-related values as sensitive and precision-critical.
- Avoid float-style arithmetic for money.
- Keep Rekening, Agent, and Shipment flows consistent with the existing admin and dashboard patterns in this repository.

=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.3
- laravel/fortify (FORTIFY) - v1
- laravel/framework (LARAVEL) - v12
- laravel/octane (OCTANE) - v2
- laravel/prompts (PROMPTS) - v0
- laravel/sanctum (SANCTUM) - v4
- livewire/flux (FLUXUI_FREE) - v2
- livewire/livewire (LIVEWIRE) - v4
- laravel/boost (BOOST) - v2
- laravel/mcp (MCP) - v0
- laravel/pail (PAIL) - v1
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- pestphp/pest (PEST) - v4
- phpunit/phpunit (PHPUNIT) - v12
- tailwindcss (TAILWINDCSS) - v4

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== tests rules ===

# Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `php artisan test --compact` with a specific filename or filter.

=== fortify/core rules ===

## Laravel Fortify

Fortify is a headless authentication backend that provides authentication routes and controllers for Laravel applications.

**Before implementing any authentication features, use the `search-docs` tool to get the latest docs for that specific feature.**

### Configuration & Setup

- Check `config/fortify.php` to see what's enabled. Use `search-docs` for detailed information on specific features.
- Enable features by adding them to the `'features' => []` array: `Features::registration()`, `Features::resetPasswords()`, etc.
- To see the all Fortify registered routes, use the `list-routes` tool with the `only_vendor: true` and `action: "Fortify"` parameters.
- Fortify includes view routes by default (login, register). Set `'views' => false` in the configuration file to disable them if you're handling views yourself.

### Customization

- Views can be customized in `FortifyServiceProvider`'s `boot()` method using `Fortify::loginView()`, `Fortify::registerView()`, etc.
- Customize authentication logic with `Fortify::authenticateUsing()` for custom user retrieval / validation.
- Actions in `app/Actions/Fortify/` handle business logic (user creation, password reset, etc.). They're fully customizable, so you can modify them to change feature behavior.

## Available Features

- `Features::registration()` for user registration.
- `Features::emailVerification()` to verify new user emails.
- `Features::twoFactorAuthentication()` for 2FA with QR codes and recovery codes.
  - Add options: `['confirmPassword' => true, 'confirm' => true]` to require password confirmation and OTP confirmation before enabling 2FA.
- `Features::updateProfileInformation()` to let users update their profile.
- `Features::updatePasswords()` to let users change their passwords.
- `Features::resetPasswords()` for password reset via email.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== laravel/v12 rules ===

# Laravel 12

- Since Laravel 11, Laravel has a new streamlined file structure which this project uses.

## Laravel 12 Structure

- In Laravel 12, middleware are no longer registered in `app/Http/Kernel.php`.
- Middleware are configured declaratively in `bootstrap/app.php` using `Application::configure()->withMiddleware()`.
- `bootstrap/app.php` is the file to register middleware, exceptions, and routing files.
- `bootstrap/providers.php` contains application specific service providers.
- The `app/Console/Kernel.php` file no longer exists; use `bootstrap/app.php` or `routes/console.php` for console configuration.
- Console commands in `app/Console/Commands/` are automatically available and do not require manual registration.

## Database

- When modifying a column, the migration must include all of the attributes that were previously defined on the column. Otherwise, they will be dropped and lost.
- Laravel 12 allows limiting eagerly loaded records natively, without external packages: `$query->latest()->limit(10);`.

### Models

- Casts can and likely should be set in a `casts()` method on a model rather than the `$casts` property. Follow existing conventions from other models.

=== livewire/core rules ===

# Livewire

- Livewire allow to build dynamic, reactive interfaces in PHP without writing JavaScript.
- You can use Alpine.js for client-side interactions instead of JavaScript frameworks.
- Keep state server-side so the UI reflects it. Validate and authorize in actions as you would in HTTP requests.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== pest/core rules ===

## Pest

- This project uses Pest for testing. Create tests: `php artisan make:test --pest {name}`.
- The `{name}` argument should not include the test suite directory. Use `php artisan make:test --pest SomeFeatureTest` instead of `php artisan make:test --pest Feature/SomeFeatureTest`.
- Run tests: `php artisan test --compact` or filter: `php artisan test --compact --filter=testName`.
- Do NOT delete tests without approval.

</laravel-boost-guidelines>
