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

- PHP ^8.1
- Laravel ^9.0|^10.0|^11.0

## 📖 Official Documentation

The detailed documentation is in the **[Laravel Like Package - Official documentation](https://docs.cslant.com/laravel-like/)**.

## 🔧 Installation

You can install the package via Composer:

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

## 📄 License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

## 🙏 Acknowledgement

This package is inspired by the [laravel-like](https://github.com/overtrue/laravel-like) package by [overtrue](https://github.com/overtrue). I have added some additional features and improvements to the package.
