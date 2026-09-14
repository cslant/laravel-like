# AGENTS.md

Instructions for AI coding agents (Claude Code, Codex, Cursor, Copilot, etc.) working in this repository.

## Project

`cslant/laravel-like` — a Laravel package adding like/dislike/love interactions to Eloquent models via a single polymorphic `likes` table. PHP 8.2–8.4, Laravel 11/12/13.

- `src/` — package source (PSR-4 `CSlant\LaravelLike\`)
- `tests/` — Pest test suite (PSR-4 `CSlant\LaravelLike\Tests\`), run against Orchestra Testbench with an in-memory SQLite DB
- `config/like.php` — published config
- `migrations/` — published migrations
- `common/helpers.php` — global helper functions (autoloaded via `files` in composer.json)

Documentation lives in a separate repo: `cslant/laravel-like-docs`. If you change public behavior (new config keys, new methods, new migrations), update the matching page there too.

## Setup

```bash
composer install
```

## Required checks before proposing any change

Run all three — a change isn't done until all three pass:

```bash
composer test       # vendor/bin/pest
composer analyse     # vendor/bin/phpstan analyse (level 9)
composer format      # vendor/bin/php-cs-fixer fix --allow-risky=yes
```

- **Tests (Pest):** every new method needs a test in `tests/Feature/` or `tests/Unit/`. Follow TDD — write the test first, watch it fail for the right reason, then implement. Use `DB::enableQueryLog()` + `expect(DB::getQueryLog())->toHaveCount(n)` to assert query counts on anything performance-sensitive (see `tests/Feature/BatchQueriesTest.php`, `tests/Feature/CacheTest.php`).
- **PHPStan (level 9, `checkModelProperties: true`):** must report zero errors. **Never** add `@phpstan-ignore` comments, baseline entries, `assert()`/`@var` overrides, or type casts just to silence an error — fix the actual type issue. If a query returns plain rows instead of hydrated models, use `->toBase()` rather than suppressing a "Access to an undefined property" error.
- **php-cs-fixer:** `@PSR12` plus repo-specific rules in `.php-cs-fixer.dist.php`. Run `composer format` to auto-fix instead of hand-formatting.

CI runs the same three checks across the PHP 8.2–8.4 / Laravel 11–13 matrix (`.github/workflows/setup_test.yml`, `phpstan.yml`, `php-cs-fixer.yml`) plus `dependabot-auto-merge.yml` and `update-changelog.yml`.

## Conventions specific to this package

- **N+1 avoidance is a design constraint, not an afterthought.** Every method that could be called in a loop over a `Collection` needs a batched counterpart (see `LikeManager::likeCountsFor()` / `userInteractionsFor()`). If you add a new per-model read, ask whether it needs a batch version too, and add a query-count test proving it.
- **Everything about the interaction model/table is config-driven**: table name (`like.table_name`), model class (`like.interaction_model`), user model/FK (`like.users.*`), UUIDs (`like.is_uuids`), count cache (`like.cache.*`). New features should follow the same pattern — no hardcoded class names or column names in `src/`.
- **`Like` and `Love` facades intentionally advertise different method subsets** in their docblocks (like/dislike vs. love) even though both resolve the same `LikeManager` singleton at runtime. Keep new methods in the docblock of the facade(s) whose domain they belong to.
- **Migrations are additive, never edited in place** once published (`2024_09_23_163615_create_likes_table.php` and later ones are live in consumer projects). A schema change is a new migration file, registered in `tests/TestCase.php`'s `getEnvironmentSetUp()` for the test suite.
- Config keys and public method signatures are commented for the *why*, not the *what* — keep that style when adding new ones.

## Git / PR discipline

- Small, focused commits. Conventional-commit-style prefixes (`feat:`, `fix:`, `chore:`, `ci:`) match existing history (`git log --oneline`).
- Don't hand-edit `CHANGELOG.md` — it's generated on release (`update-changelog.yml`).
- Don't bump the version anywhere; releases are tagged separately.
