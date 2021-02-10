# laravel-activitylog-ui

A UI for [Spatie's](https://spatie.be/) awesome [laravel-activitylog](https://github.com/spatie/laravel-activitylog) package that we all love.

## Features

The UI was intentionally designed to fit the purpose of usage for audit trail. Hence, the following features are currently available:

- Models are searchable
- Nice, beautiful UI

## Installation

- Ensure you have properly installed [Spatie's laravel-activitylog package](https://github.com/spatie/laravel-activitylog#installation) and [Voyager](https://github.com/spatie/tcg/voyager) and run your migrations

- Install this package into your Laravel project via composer:

```
composer require damms005/laravel-activitylog-ui
```

This package automatically registers a route for you at `/admin/activitylog-ui`. You can now visit `https://<yourwebsite.app.io>/admin/activitylog-ui` to start filtering/auditing your activity logs

## Todo

- Write tests

## License

The MIT License (MIT). Please see License File for more information.
