# laravel-like
User 👍 like and 👎 dislike features for Laravel Application.

## Installation

You can install the package via composer:

```bash
composer require cslant/laravel-like
```

## Configuration and Migrations

You can publish all the necessary configuration and migration files by running the following command:

```bash
php artisan vendor:publish --provider="CSlant\LaravelLike\LikeServiceProvider"
```

After the configuration file has been published, you can run the migration:

```bash
php artisan migrate
```
