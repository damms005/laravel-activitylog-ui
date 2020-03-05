# laravel-activitylog-ui

A UI for [Spatie's](https://spatie.be/) awesome [laravel-activitylog](https://github.com/spatie/laravel-activitylog) package that we all love.

## Features

The UI was intentionally designed to fit the purpose of usage for audit trail. Hence, the following features are currently available:

- Models are searchable
- Nice, beautiful UI

## Installation

- Ensure you have properly installed [laravel-activitylog](https://github.com/spatie/laravel-activitylog#installation)

- Install this package into your Laravel project via composer:

```
composer require damms005/laravel-activitylog-ui
```

The package will automatically register itself.

Publish the assets:

```
php artisan vendor:publish --provider="Damms005\ActivitylogUi\ActivitylogUiServiceProvider" --tag="migrations"
```

## Integration ideas

- Voyager
- Tailwindcss

## Testing

- Write tests

## Security

If you discover any security related issues, please email freek@spatie.be instead of using the issue tracker.

## Credits

[The Spatie team ❤](https://spatie.be/) and [All Contributors](conts)

## License

The MIT License (MIT). Please see License File for more information.
