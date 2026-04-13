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
