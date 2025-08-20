# Laravel Paseto

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mydaniel/laravel-paseto.svg?style=flat-square)](https://packagist.org/packages/mydaniel/laravel-paseto)
[![Total Downloads](https://img.shields.io/packagist/dt/mydaniel/laravel-paseto.svg?style=flat-square)](https://packagist.org/packages/mydaniel/laravel-paseto)
[![License](https://img.shields.io/packagist/l/mydaniel/laravel-captcha.svg?style=flat-square)](https://github.com/daniyousefifar/laravel-captcha/blob/master/LICENSE)

This package provides a **PASETO (Platform-Agnostic Security Tokens)** authentication guard for Laravel. It offers a modern, secure, and easy-to-use alternative to JWT (JSON Web Tokens).

Paseto tokens are encrypted and authenticated, providing better security guarantees than JWT out of the box.

## Features

- ✅ Secure, stateless authentication for your Laravel applications.
- ✅ PASETO v4 Local support (symmetric key authenticated encryption).
- ✅ Easy to configure and use.
- ✅ Token blacklist functionality to invalidate tokens upon logout.
- ✅ An artisan command to generate a secure secret key.
- ✅ Fully customizable through configuration and contracts.

## Installation

You can install the package via composer:

```bash
composer require mydaniel/laravel-paseto
```

## Configuration

1.  **Publish the configuration file:**

    This will create a `config/paseto.php` file in your project. You can customize token expiration, issuer, audience, and other claims here.

    ```bash
    php artisan vendor:publish --provider="MyDaniel\Paseto\PasetoServiceProvider" --tag="config"
    ```

2.  **Generate a Secret Key:**

    Run the following artisan command to generate a secure, 32-byte hex-encoded key. The command will automatically add it to your `.env` file.

    ```bash
    php artisan paseto:generate-key
    ```

    This will add a line like this to your `.env` file:
    ```env
    PASETO_SECRET_KEY="your-generated-secret-key"
    ```

3.  **Configure the Auth Guard:**

    Open your `config/auth.php` file and make the following changes:

    ```php
    'defaults' => [
        'guard' => 'api', // Or your default guard
        'passwords' => 'users',
    ],

    'guards' => [
        'api' => [
            'driver' => 'paseto',
            'provider' => 'users',
        ],
    ],
    ```

## Usage

### 1. Preparing Your User Model

Add the `MyDaniel\Paseto\Contracts\PasetoSubject` contract and the `MyDaniel\Paseto\Traits\HasPaseto` trait to your `User` model.

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use MyDaniel\Paseto\Contracts\PasetoSubject;
use MyDaniel\Paseto\Traits\HasPaseto;

class User extends Authenticatable implements PasetoSubject
{
    use HasPaseto;

    // ... your model properties and methods
}
```

The `PasetoSubject` contract ensures your model has the necessary methods for generating token claims. The `HasPaseto` trait provides a ready-to-use implementation for these methods.

### 2. Generating a Token

After authenticating a user (e.g., in a login controller), you can generate a token for them.

```php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $token = $user->generateToken();

        return response()->json(['token' => $token]);
    }
}
```

### 3. Protecting Routes

Use the `auth:api` middleware in your `routes/api.php` file to protect routes that require authentication.

```php
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
```

Clients should send the token in the `Authorization` header as a Bearer token:

```
Authorization: Bearer <your-paseto-token>
```

### 4. Logging Out (Invalidating a Token)

To invalidate a token, you can use the `logout` method from the `Auth` facade. This will add the token's unique identifier (`jti`) to the blacklist for its remaining lifetime.

```php
public function logout()
{
    Auth::logout();

    return response()->json(['message' => 'Successfully logged out']);
}
```
**Note:** The blacklist feature requires a cache driver. By default, it uses your application's default cache driver. You can specify a different cache store in `config/paseto.php`.

## Customizing Token Claims

You can add custom claims to your tokens by implementing the `getJwtCustomClaims` method in your `User` model.

```php
// In App\Models\User
public function getJwtCustomClaims(): array
{
    return [
        'foo' => 'bar',
        'role' => $this->role,
    ];
}
```

## Contributing

Contributions are welcome! Please feel free to submit a pull request on [GitHub](https://github.com/daniyousefifar/laravel-paseto).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
