# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
composer check                         # CS + PHPStan + tests
composer test                          # All tests
vendor/bin/phpunit tests/TestCase/Command/ListCommandTest.php  # Single file
vendor/bin/phpunit --filter testExecute  # Single test
composer cs-check                      # Coding standards check
composer cs-fix                        # Auto-fix coding standards
composer phpstan                       # Static analysis (requires initial: composer stan-setup)
```

## Architecture

CakePHP 5.x plugin for URL-safe slug validation and reserved-word collision prevention.

- **`ReservedSlugsTable`** — Primary key is `slug` column (string PK, not auto-increment)
- **`SlugsSyncService`** — import/sync run inside a DB transaction; uses `SlugsFileLoader` for seed file resolution
- **Seed file priority**: (1) `Configure('ReservedSlugs.syncFile')` → (2) `CONFIG/reserved-slugs.txt` → (3) plugin built-in `config/reserved-slugs.txt`
- **Plugin class** enables console only (bootstrap/middleware/routes disabled)

## Test Setup

- SQLite in-memory by default (override with `DB_URL` env var)
- Schema: `tests/schema.php` (CakePHP SchemaLoader)