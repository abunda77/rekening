# Agent Instructions

Laravel 12 + Livewire 4 + Flux UI (free) + Tailwind CSS 4 + Pest 4

## Priority references

- Global project rules: `.ai/guidelines/rekening.md`
- Domain skill: `.ai/skills/laravel-rekening/SKILL.md`

## Commands

**Development server** (server + queue + vite together):
```bash
composer run dev
```

**Testing**:
```bash
php artisan test --compact                              # all tests
php artisan test --compact tests/Feature/SomeTest.php   # one file
php artisan test --compact --filter=testName            # one test
```

**Code formatting** (run before finishing):
```bash
vendor/bin/pint --dirty
```

**Frontend**: If UI changes don't appear, run `npm run build` or `composer run dev`.

**Composer test suite** (clears config + pint test + tests):
```bash
composer run test
```

## Architecture

**Dual authentication**: Admin users and agents have separate auth flows.
- Agent portal routes are prefixed with `/agent*`
- Agent auth guard redirects to `agent.login` route
- Check `bootstrap/app.php` for redirect logic

**Authorization**: Spatie Laravel Permission (roles & permissions).

**Testing database**: Tests use SQLite in-memory (`phpunit.xml`). Production uses MySQL.

**Default agent credentials** (from `AgentSeeder`):
- Agent Code: `KSP987`
- Password: `password`

## Conventions

- Internal code: English
- User-facing text (labels, messages, notifications): Indonesian
- Use `--no-interaction` flag with Artisan generators
- Reuse Flux UI components before building custom alternatives
- Preserve dark mode support in all views

## Verification workflow

1. Write or update Pest test for meaningful changes
2. Run `vendor/bin/pint --dirty`
3. Run relevant tests with `php artisan test --compact`
4. Verify frontend with `npm run build` if needed
