# laravel-like
User 👍 like and 👎 dislike features for Laravel Application.

## 📝 Introduction

This package provides a simple way to add like 👍, dislike 👎, and love ❤️ or unlike 🤚 features to your Laravel application.

It is easy to use and can be customized to fit your needs.

## 📋 Requirements

- PHP ^8.1
- Laravel ^9.0|^10.0|^11.0

## 🔧 Installation

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
