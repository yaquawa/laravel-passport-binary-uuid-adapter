## Introduction
By default [Passport](https://laravel.com/docs/master/passport) does not support using binary UUID.

This package acts as an adapter to allow Passport to be able to use binary UUID.

**Notice:** this package assumes you are using [ramsey/uuid](https://github.com/ramsey/uuid) to generate UUID.

## Usage
First, pull in the package through Composer.

```bash
composer require yaquawa/laravel-passport-binary-uuid-adapter
```

This package is going to bind the `Laravel\Passport\Bridge\AccessTokenRepository` and `Laravel\Passport\ApiTokenCookieFactory` before Passport `make` it.

So to make it works, this package must be loaded before Passport's service provider.

To guarantee this package's service provider to be loaded before Passport's service provider, in the `extra` section of your application's `composer.json` file, add passport to the `dont-discover` list.

```
"extra": {
    "laravel": {
        "dont-discover": [
            "laravel/passport"
        ]
    }
}
```

Then manually add `Laravel\Passport\PassportServiceProvider::class` to `providers` in `config/app.php`.

Lastly, change your user provider to `eloquent_with_binary_uuid` in your `config/auth.php`.

```
'providers' => [
    'users' => [
        'driver' => 'eloquent_with_binary_uuid',
        'model'  => App\Models\User::class,
    ]
]
```

Furthermore, you can specify an array with `model`, in that case, the user provider will attempt to retrieve the user from different tables. This is useful when you want to use the same endpoint for a variety of users. For example, if you give both `User` and `Admin` model class to the `model` key, it'll first try to find a `User` from its table, if it can't then try to find one from `Admin` 's table using the same user credentials from the incoming request.

## Contributing
I'm not sure if Laravel is going to allow us to use binary UUID in the future. So I'll try to align this package to the latest version of Laravel and Passport as soon as I can. 
Please if you discover any issues, feel free to send me a PR 🤝.