---
name: laravel-rekening
description: Build and maintain Rekening features in this Laravel 12 app, including Livewire screens, Flux UI, financial workflows, and Pest coverage.
---

# Laravel Rekening

## When to use this skill

Use this skill when working on Rekening domain features in this repository, especially Livewire CRUD screens, account and agent flows, Blade layouts, and financial data handling.

## Project conventions

- Follow existing patterns in sibling files before introducing a new structure.
- Keep class names, methods, properties, and internal code in English.
- Keep labels, helper text, validation messages, and notifications in Indonesian unless the surrounding screen already uses English.
- Reuse existing Flux UI and Blade patterns before adding custom markup.
- Preserve dark mode and responsive behavior when editing views.

## Laravel and Livewire workflow

- Prefer Laravel conventions, Eloquent relationships, named routes, and Form Requests where controller validation is needed.
- Validate Livewire actions on the server and keep component state authoritative on the backend.
- Use eager loading for related models to avoid N+1 queries.
- For Laravel ecosystem questions, use Laravel Boost documentation search before guessing framework behavior.
- In Laravel 12, middleware and console setup belong in `bootstrap/app.php` and related modern structure, not legacy kernel files.

## Rekening-specific guidance

- Treat money and account-related values as sensitive and precision-critical.
- Avoid float-style arithmetic for currency calculations.
- Keep agent, account, and shipment related UI flows consistent with existing admin screens.
- Prefer incremental changes that match the current CRUD and dashboard patterns already present in the repo.

## Verification

- Add or update a Pest test for behavior changes when the change is testable.
- Run the smallest relevant test command with `php artisan test --compact`.
- Run `vendor/bin/pint --dirty` before finishing code changes.
- If a frontend update is not visible, rebuild assets with `npm run build` or run the dev process.
