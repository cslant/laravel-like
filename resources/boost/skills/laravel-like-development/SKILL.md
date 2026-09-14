---
name: laravel-like-development
description: "Implement like, dislike, and love interactions, batch counting, and interaction-state checks in cslant/laravel-like."
license: MIT
metadata:
  author: cslant
---

# Laravel Like Development

**Skill ID:** `laravel-like-development`
**Package:** `cslant/laravel-like`
**Purpose:** Like/dislike/love interactions for any Eloquent model via one polymorphic `likes` table, with N+1-safe batch APIs and optional count caching.

---

## Quick Start

```bash
# Install
composer require cslant/laravel-like

# Publish config + migrations
php artisan vendor:publish --provider="CSlant\LaravelLike\Providers\LikeServiceProvider"

# Run migrations
php artisan migrate
```

There are no HTTP routes and no `.env` variables — this package is purely a service + traits + one
table; all behaviour is driven by `config/like.php`.

## What This Package Does

**Key Features:**
- Multiple interaction types — like, dislike, love; one active type per user per model, enforced
  transactionally (`LikeManager::setInteraction()`)
- Facade & Service API — `Like`/`Love` facades plus a `LikeManager` singleton behind
  `Contracts\LikeManager`
- Idempotent actions — calling `like()` twice returns the same row, never duplicates
- Polymorphic — works with any Eloquent model via `model_id`/`model_type`
- Optional UUID primary keys (`is_uuids`), driven entirely by config
- Batch APIs for lists — `likeCountsFor()` / `userInteractionsFor()` resolve counts/state for a
  whole `Collection` in one query per distinct model type, instead of one query per row
- Optional count caching (`cache.enabled` / `cache.ttl`) for `likesCount()`/`dislikesCount()`/
  `lovesCount()`, auto-invalidated on every write
- Composite index `(model_type, model_id, type)` shipped by default alongside the unique
  `(user_id, model_id, model_type, type)` index

## Configuration

### Config File (actual shape of `config/like.php`)

```php
return [
    'is_uuids' => false,               // uuid primary key + morph columns instead of auto-increment

    'table_name' => 'likes',

    'interaction_model' => 'CSlant\LaravelLike\Models\Like',

    'users' => [
        'model' => null,                // null -> falls back to config('auth.providers.users.model')
        'foreign_key' => 'user_id',
    ],

    'cache' => [
        'enabled' => false,             // caches likesCount()/dislikesCount()/lovesCount()
        'ttl' => 60,                    // seconds; safety net only, writes invalidate immediately
    ],
];
```

There is **no** `.env` layer in the published config — every key above is a plain array value, not
`env(...)`. If a host app wants env-driven config, it edits the published `config/like.php` itself.

## Usage

### Model Setup

```php
use CSlant\LaravelLike\HasLike;   // adds like + dislike
use CSlant\LaravelLike\HasLove;   // adds love (separate, optional trait)

class Post extends Model
{
    use HasLike;
    use HasLove;
}
```

```php
use CSlant\LaravelLike\UserHasInteraction;

class User extends Authenticatable
{
    use UserHasInteraction;   // adds likes(): HasMany + forgetInteractions()/forgetInteractionsOfType()
}
```

**Never** put `UserHasInteraction` on a model that also uses `HasLike`/`HasLove` — both declare a
`likes()` relation with incompatible signatures (`HasMany` vs `MorphMany`).

### Facade (`Like` vs `Love` — different advertised methods, same singleton)

Both facades resolve the same `LikeManager` singleton at runtime; their docblocks only differ so
IDEs/PHPStan suggest the right domain:

```php
use CSlant\LaravelLike\Facades\Like;
use CSlant\LaravelLike\Facades\Love;

Like::like($post, $userId = null);   // $userId omitted -> auth()->id()
Like::dislike($post);
Like::unlike($post);
Like::unDislike($post);
Like::toggle($post);                 // none -> like -> none; dislike -> like; love untouched
Like::isLiked($post);
Like::isDisliked($post);
Like::likesCount($post);
Like::dislikesCount($post);
Like::totalCount($post);             // likes + dislikes + loves — facade/manager only, no $post->totalCount()

Love::love($post);
Love::unlove($post);
Love::isLoved($post);
Love::lovesCount($post);
```

Passing no `$userId` and having no authenticated user throws
`Illuminate\Auth\AuthenticationException`. Passing an unpersisted `$model` throws
`InvalidArgumentException`.

### Batch APIs (avoid N+1 on listings)

```php
use CSlant\LaravelLike\Enums\InteractionTypeEnum;

// One query per distinct model type in $posts, not one per post
$counts = Like::likeCountsFor($posts, InteractionTypeEnum::LIKE);   // [modelKey => count]

// One query per distinct model type for the given user's state
$interactions = Like::userInteractionsFor($posts, $userId);          // keyed "morphClass:modelId"

// Already batched: 1 query for the user's likes + 1 per distinct model type
$likedModels = Like::userLikedModels($userId);
```

`$counts` and `$interactions` omit entries for models with no matching row — default with
`$counts[$post->getKey()] ?? 0`.

### Model helpers (same actions, no explicit facade)

```php
$post->like(); $post->dislike(); $post->love(); $post->toggle();
$post->isLiked(); $post->isLikedBy($userId); $post->isDisliked(); $post->isLoved();
$post->likesCount(); $post->dislikesCount(); $post->likesCountDigital(); // 1200 -> '1.2K'
$post->lovesCount(); // requires HasLove
```

### Querying the `Like` model / relations directly

```php
use CSlant\LaravelLike\Models\Like;

Like::withModelType(Post::class)->where('type', 'like')->count();
$post->likes()->count();          // all interaction types
$post->likesTo()->count();        // MorphMany, type = like only
$post->likeTo;                    // MorphOne, current like row (null if none)
$post->likeOne();                 // MorphOne base relation (no type filter)
```

Eager-load instead of looping: `Post::with('likes.user')`, `Post::withCount(['likesTo as likes_count'])`.

## Architecture

```
src/
├── LikeManager.php                  # like/dislike/love, toggle, counts, batch APIs, count cache
├── Contracts/LikeManager.php        # interface bound as a singleton
├── Providers/LikeServiceProvider.php
├── Facades/
│   ├── Like.php                     # advertises like/dislike/unlike/unDislike/toggle/isLiked/isDisliked/likesCount/dislikesCount
│   └── Love.php                     # advertises love/unlove/isLoved/lovesCount
├── Models/Like.php                  # HasUuids (config-gated), morphTo model, belongsTo user, scopeWithModelType
├── Enums/InteractionTypeEnum.php    # NEUTRAL | LIKE | DISLIKE | LOVE (backed string enum)
├── Traits/
│   ├── InteractionRelationship.php  # likeOne()/likes() morph relations + like()/dislike()/love()/toggle()/isX() delegating to LikeManager
│   ├── ForgetsInteractions.php      # forgetInteractions()/forgetInteractionsOfType()
│   ├── Like/LikeCount.php           # likesCount()/dislikesCount()/*CountDigital()
│   ├── Like/LikeScopes.php          # likeTo()/dislikeTo()/likesTo()/dislikesTo()/isLikedBy()/isDislikedBy()
│   ├── Love/LoveCount.php           # lovesCount()/lovesCountDigital()
│   └── Love/LoveScopes.php          # loveTo()/lovesTo()/isLovedBy()
├── HasLike.php                      # composes InteractionRelationship + Like/{LikeCount,LikeScopes}
├── HasLove.php                      # composes InteractionRelationship + Love/{LoveCount,LoveScopes}
└── UserHasInteraction.php           # for the User model only: likes(): HasMany + ForgetsInteractions
config/like.php
migrations/
├── <ts>_create_likes_table.php                    # table + unique(user_id, model_id, model_type, type)
└── <ts>_add_type_lookup_index_to_likes_table.php  # index(model_type, model_id, type)
common/helpers.php                    # count_digital() — 1200 -> '1.2K', 999500+ -> '1M'
```

## Development Skills

### 1. Adding a new interaction type

`InteractionTypeEnum` is a backed string enum; `LikeManager` branches on it with `isLike()`/
`isDislike()`/`isLove()` predicates rather than a generic switch. A 4th type needs: a new enum case,
a new `isX()` predicate, an entry in `getValues()`/`getValuesAsStrings()`, and explicit handling
everywhere the code currently branches on type — `toggle()` in particular only cycles
`LIKE`⇄`DISLIKE`/none today and silently leaves any other type (including `LOVE`) untouched.

### 2. Adding a batch method for a new per-model read

Follow the shape of `likeCountsFor()`/`userInteractionsFor()`: group the input `Collection` by
`getMorphClass()`, run **one query per group** with `whereIn('model_id', ...)`, then merge results
keyed by primary key (or `"{morphClass}:{modelId}"` when the result must disambiguate mixed model
types in one collection). Add a test with `DB::enableQueryLog()` asserting the query count stays
flat as the collection grows — see `tests/Feature/BatchQueriesTest.php`.

### 3. Extending the interaction model

```php
// config/like.php
'interaction_model' => \App\Models\CustomLike::class,
```
```php
namespace App\Models;

use CSlant\LaravelLike\Models\Like;

class CustomLike extends Like
{
    // extra columns (with a matching migration), relationships, or logic
}
```

**Rules:**
- Never hardcode `'likes'`, `'user_id'`, or `Like::class` in `src/` — always resolve through
  `config('like.*')`, matching every existing method (`userForeignKey()`, `interactionModel()`, etc.).
- Keep `Like`'s facade docblock scoped to like/dislike methods and `Love`'s to love methods — both
  still resolve the same `LikeManager` singleton at runtime, so this is a documentation/IDE
  boundary, not a runtime guard; don't try to enforce it with `__callStatic` overrides without an
  explicit decision to do so.

## Best Practices

1. **N+1 discipline** — never call `likesCount()`/`dislikesCount()`/`lovesCount()`/`isLiked()`/
   `isDisliked()`/`isLoved()` inside a `foreach`/`map` over more than one model; use
   `likeCountsFor()`/`userInteractionsFor()`, or Eloquent's own `withCount()` when already building
   the query.
2. **Config-driven, not hardcoded** — table name, interaction model class, user model/FK, UUIDs,
   and cache are all `config('like.*')`; new code must not assume the defaults.
3. **Cache is opt-in and self-invalidating** — `cache.enabled` defaults to `false`; if you add a new
   write path (beyond `like/dislike/love/unlike/unDislike/unlove/toggle`), it must call
   `forgetCounts($model)` too, or a host app with caching enabled will see stale counts.
4. **`toggle()` is like/dislike-only** — it does not create or remove `love`; don't assume it is a
   generic 3-state cycle across all interaction types.
5. **Migrations are additive** — never edit a published migration in place (consumers already ran
   it); ship a new migration file and register it in `tests/TestCase.php`'s
   `getEnvironmentSetUp()` for the test suite.

## Use Cases

1. Like/dislike buttons on posts, comments, or products
2. "Love" / favorite / bookmark surfaces via the separate `HasLove` trait
3. Most-liked / trending content rankings (`withCount()` + `orderBy()`)
4. "Liked by you" state on a feed without per-row queries (`userInteractionsFor()`)
5. A user's liked-content history across multiple model types (`userLikedModels()`)

## Related Packages

- Requires: `illuminate/support`, `illuminate/database` (`^11.0|^12.0|^13.0`); PHP `^8.2|^8.4`.
- Inspired by [overtrue/laravel-like](https://github.com/overtrue/laravel-like) — this package adds
  dislike, love, batch APIs, and count caching on top.
- Full docs: https://docs.cslant.com/laravel-like

## Recent updates (2026-09-15) — batch APIs, count caching, lookup index

- Added `LikeManager::likeCountsFor()` and `userInteractionsFor()` — batched, N+1-safe counterparts
  to `likesCount()`/`isLiked()` for use on lists of models.
- Added optional `cache.enabled`/`cache.ttl` config — caches `likesCount()`/`dislikesCount()`/
  `lovesCount()`, auto-invalidated on every write (`like`/`dislike`/`love`/`unlike`/`unDislike`/
  `unlove`/`toggle`).
- Added a migration for a composite index `(model_type, model_id, type)`, shipped alongside the
  original `create_likes_table` migration's unique `(user_id, model_id, model_type, type)` index.
- Split the `Like`/`Love` facade docblocks to their own domains — same runtime singleton, narrower
  IDE/PHPStan surface per facade.
