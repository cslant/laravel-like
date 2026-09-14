# laravel-like

The interaction for User 👍 like, 👎 dislike, and love ❤️ features for Laravel Application.

<img src="https://github.com/cslant/laravel-like-docs/blob/main/assets/public/images/laravel-like-thumb.webp" alt="Laravel Like Package">

<p align="center">
<a href="#"><img src="https://img.shields.io/github/license/cslant/laravel-like.svg?style=flat-square" alt="License"></a>
<a href="https://github.com/cslant/laravel-like/releases"><img src="https://img.shields.io/github/release/cslant/laravel-like.svg?style=flat-square" alt="Latest Version"></a>
<a href="https://packagist.org/packages/cslant/laravel-like"><img src="https://img.shields.io/packagist/dt/cslant/laravel-like.svg?style=flat-square" alt="Total Downloads"></a>
<a href="https://github.com/cslant/laravel-like/actions/workflows/setup_test.yml"><img src="https://img.shields.io/github/actions/workflow/status/cslant/laravel-like/setup_test.yml?label=tests&branch=main" alt="Test Status"></a>
<a href="https://github.com/cslant/laravel-like/actions/workflows/php-cs-fixer.yml"><img src="https://img.shields.io/github/actions/workflow/status/cslant/laravel-like/php-cs-fixer.yml?label=code%20style&branch=main" alt="Code Style Status"></a>
<a href="https://scrutinizer-ci.com/g/cslant/laravel-like"><img src="https://img.shields.io/scrutinizer/g/cslant/laravel-like.svg?style=flat-square" alt="Quality Score"></a>
</p>

## 📝 Introduction

This package provides an interaction way to add like 👍, dislike 👎, and love ❤️ features to your Laravel application.

It is easy to use and can be customized to fit your needs.

## 📋 Requirements

- PHP ^8.2
- Laravel ^11.0|^12.0|^13.0

## 📖 Official Documentation

The detailed documentation is in the **[Laravel Like Package - Official documentation](https://docs.cslant.com/laravel-like)**.

## 🔧 Installation

You can install the package via Composer:

```bash
composer require cslant/laravel-like
```

## Configuration and Migrations

You can publish all the necessary configuration and migration files by running the following command:

```bash
php artisan vendor:publish --provider="CSlant\LaravelLike\Providers\LikeServiceProvider"
```

After the configuration file has been published, you can run the migration:

```bash
php artisan migrate
```

## 🛠️ Usage

### Facade

`Like` and `Love` both proxy the same `LikeManager` singleton, kept as separate facades so each stays focused on its own domain (like/dislike vs. love):

```php
use CSlant\LaravelLike\Facades\Like;
use CSlant\LaravelLike\Facades\Love;

$post->like();               // or Like::like($post)
Like::dislike($post);
Like::unlike($post);
Like::toggle($post);

Like::isLiked($post);        // bool
Like::isDisliked($post);     // bool

Like::likesCount($post);     // int
Like::dislikesCount($post);  // int

Love::love($post);            // or $post->love()
Love::unlove($post);
Love::isLoved($post);        // bool
Love::lovesCount($post);     // int
```

By default the acting user is resolved via `auth()->id()`. Pass an explicit user id as the second argument to override:

```php
Like::like($post, $userId);
```

### Model traits

Add `HasLike` to an interactable model:

```php
use CSlant\LaravelLike\HasLike;

class Post extends Model
{
    use HasLike;
}
```

Then:

```php
$post = Post::find(1);

$post->like();
$post->dislike();
$post->love();
$post->toggle();

$post->isLiked();
$post->isLikedBy($userId);

$post->likesCount();
$post->dislikesCountDigital();
```

Use `HasLove` (and/or `UserHasInteraction` on the user model) for love-only surfaces.

## ⚡ Performance

All counts are a single `COUNT` query, and predicates use `EXISTS`, so a single call never causes an N+1. But calling `likesCount()` or `isLiked()` **in a loop** over a list still does — use the batch APIs instead:

```php
use CSlant\LaravelLike\Facades\Like;
use CSlant\LaravelLike\Enums\InteractionTypeEnum;

$posts = Post::limit(50)->get();

// One query total instead of one per post
$counts = Like::likeCountsFor($posts, InteractionTypeEnum::LIKE); // [postId => count]

// One query total for the current user's state across all posts
$interactions = Like::userInteractionsFor($posts, auth()->id());  // keyed by "morphClass:modelId"
```

Or, when you're already building the query, prefer Eloquent's own `withCount()`:

```php
$posts = Post::withCount(['likesTo as likes_count', 'dislikesTo as dislikes_count'])->get();
```

Querying a user's liked models performs one query per distinct model type (a bounded cost inherent to polymorphic relations):

```php
$models = Like::userLikedModels($userId); // 1 + (number of model types) queries
```

When listing interactions, eager-load related models to avoid per-row queries:

```php
$user->likes()             // hasMany, from the UserHasInteraction trait
    ->with('model')        // eager-load the interactable model
    ->get();
```

Read-heavy counts can also be cached — set `cache.enabled` to `true` in `config/like.php` and the package invalidates it automatically on every write. See the [Performance docs](https://docs.cslant.com/laravel-like/usage/performance) for the full picture.

## 🤖 AI agent skill

This repo ships a [Claude Code skill](.claude/skills/laravel-like/SKILL.md) summarizing safe usage patterns (the batch APIs, the N+1 pitfalls, the facade split). If your project uses this package and you use Claude Code, copy `.claude/skills/laravel-like/` from this repo into your own project's `.claude/skills/` directory so your AI assistant applies it automatically.

## 📄 License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

## 🙏 Acknowledgement

This package is inspired by the [laravel-like](https://github.com/overtrue/laravel-like) package by [overtrue](https://github.com/overtrue). I have added some additional features and improvements to the package.
