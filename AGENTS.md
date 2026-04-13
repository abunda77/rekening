# Agent Instructions

This repository is a Laravel 12 application with Livewire 4, Flux UI, Tailwind CSS 4, Pest, Fortify, Sanctum, Octane, and Laravel Boost.

## Priority references

- Global project rules: `.ai/guidelines/rekening.md`
- Domain skill for Rekening work: `.ai/skills/laravel-rekening/SKILL.md`

Use those files as the primary source for project-specific conventions to avoid duplicating instructions in multiple places.

## Working rules

- Follow existing sibling-file patterns before introducing a new structure.
- Prefer Laravel-native solutions and idiomatic framework conventions.
- Do not add dependencies, new top-level folders, or documentation files unless explicitly requested.
- Keep internal code in English and user-facing copy in Indonesian unless the surrounding UI already uses English.
- Reuse existing Flux UI, Blade, and Livewire patterns before building custom alternatives.

## Laravel Boost

- Use Laravel Boost tools when available.
- Search Laravel ecosystem documentation with Boost before guessing framework behavior.
- Use Artisan generators with `--no-interaction` when creating Laravel files.
- Use Boost database and browser inspection tools when debugging is needed.

## Verification

- Every meaningful code change should be covered by the smallest relevant Pest test.
- Run `php artisan test --compact` with a file path or filter when applicable.
- Run `vendor/bin/pint --dirty` before finishing code changes.
- If frontend changes do not appear, rebuild assets with `npm run build`, `npm run dev`, or `composer run dev`.
