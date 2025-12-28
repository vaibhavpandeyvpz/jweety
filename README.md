# jweety

[![Latest Version](https://img.shields.io/packagist/v/vaibhavpandeyvpz/jweety.svg?style=flat-square)](https://packagist.org/packages/vaibhavpandeyvpz/jweety)
[![Downloads](https://img.shields.io/packagist/dt/vaibhavpandeyvpz/jweety.svg?style=flat-square)](https://packagist.org/packages/vaibhavpandeyvpz/jweety)
[![PHP Version](https://img.shields.io/packagist/php-v/vaibhavpandeyvpz/jweety.svg?style=flat-square)](https://packagist.org/packages/vaibhavpandeyvpz/jweety)
[![License](https://img.shields.io/packagist/l/vaibhavpandeyvpz/jweety.svg?style=flat-square)](LICENSE)
[![Build Status](https://img.shields.io/github/actions/workflow/status/vaibhavpandeyvpz/jweety/tests.yml?branch=master&style=flat-square)](https://github.com/vaibhavpandeyvpz/jweety/actions)

A simple, modern [JWT](https://jwt.io/) ([RFC 7519](https://tools.ietf.org/html/rfc7519)) encoding/decoding and validation library for PHP 8.2+.

## Features

- ✅ **RFC 7519 Compliant** - Full support for JSON Web Token standard
- ✅ **Modern PHP 8.2+** - Uses latest PHP features (enums, readonly properties, match expressions)
- ✅ **HMAC Algorithms** - Supports HS256, HS384, and HS512
- ✅ **Type Safe** - Comprehensive type hints and strict types
- ✅ **Claim Validation** - Automatic validation of `exp`, `iat`, and `nbf` claims
- ✅ **PSR-20 Clock Support** - Use any PSR-20 clock implementation for testable time-based validation
- ✅ **Exception Handling** - Specific exception types for different error scenarios
- ✅ **100% Test Coverage** - Fully tested with comprehensive test suite

## Installation

Install via Composer:

```bash
composer require vaibhavpandeyvpz/jweety
```

## Quick Start

### Creating a Token

```php
use Jweety\Encoder;
use Jweety\Algorithm;

// Initialize encoder with secret key
$encoder = new Encoder('your-secret-key-here');

// Create claims
$claims = [
    'sub' => 'user123',
    'name' => 'John Doe',
    'iat' => time(),
    'exp' => time() + 3600, // Expires in 1 hour
];

// Generate token (defaults to HS256)
$token = $encoder->stringify($claims);
echo $token; // eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...
```

### Parsing and Validating a Token

```php
use Jweety\Encoder;
use Jweety\Exception\InvalidTokenException;
use Jweety\Exception\TokenExpiredException;
use Jweety\Exception\InvalidSignatureException;

$encoder = new Encoder('your-secret-key-here');

try {
    // Parse and validate token (validates signature and claims by default)
    $claims = $encoder->parse($token);

    echo $claims->sub;    // 'user123'
    echo $claims->name;    // 'John Doe'

} catch (TokenExpiredException $e) {
    // Token has expired
    echo "Token expired: " . $e->getMessage();

} catch (InvalidSignatureException $e) {
    // Signature verification failed
    echo "Invalid signature: " . $e->getMessage();

} catch (InvalidTokenException $e) {
    // Token is malformed or invalid
    echo "Invalid token: " . $e->getMessage();
}
```

### Parsing Without Claim Validation

```php
// Parse token without validating claims (exp, iat, nbf)
$claims = $encoder->parse($token, false);
```

## Usage Examples

### Using Different Algorithms

```php
use Jweety\Encoder;
use Jweety\Algorithm;

$encoder = new Encoder('your-secret-key');

// Using enum
$token = $encoder->stringify($claims, Algorithm::HS256);

// Using string
$token = $encoder->stringify($claims, 'HS384');

// Using HS512
$token = $encoder->stringify($claims, Algorithm::HS512);
```

### Restricting Allowed Algorithms

```php
use Jweety\Encoder;
use Jweety\Algorithm;

// Only allow HS256
$encoder = new Encoder('your-secret-key', Algorithm::HS256);

// Allow multiple algorithms
$encoder = new Encoder('your-secret-key', [Algorithm::HS256, Algorithm::HS384]);

// Using strings
$encoder = new Encoder('your-secret-key', ['HS256', 'HS512']);
```

### Using PSR-20 Clock for Testing

The library supports PSR-20 Clock implementations for controllable time in tests:

```php
use Jweety\Encoder;
use Samay\FrozenClock; // or any PSR-20 Clock implementation

// Create a frozen clock for testing
$clock = new FrozenClock(new \DateTimeImmutable('2024-01-01 12:00:00'));
$encoder = new Encoder('your-secret-key', [Algorithm::HS256], $clock);

// Now all time-based validations use the frozen time
$claims = [
    'sub' => 'user123',
    'exp' => $clock->now()->getTimestamp() + 3600, // 1 hour from frozen time
];

$token = $encoder->stringify($claims);
$parsed = $encoder->parse($token); // Uses frozen clock for validation
```

### Custom Token Type

```php
$token = $encoder->stringify($claims, Algorithm::HS256, 'CustomType');
```

### Manual Claim Validation

```php
use Jweety\Encoder;
use Jweety\Exception\TokenExpiredException;

$encoder = new Encoder('your-secret-key');

// Parse without validation
$claims = $encoder->parse($token, false);

// Manually validate claims
try {
    $encoder->assert($claims);
    echo "Token is valid";
} catch (TokenExpiredException $e) {
    echo "Token expired";
}
```

### Working with Standard JWT Claims

```php
$claims = [
    // Subject - The user ID
    'sub' => 'user123',

    // Issued At - When the token was issued
    'iat' => time(),

    // Expiration Time - When the token expires
    'exp' => time() + 3600,

    // Not Before - Token cannot be used before this time
    'nbf' => time(),

    // Issuer - Who issued the token
    'iss' => 'https://example.com',

    // Audience - Who the token is intended for
    'aud' => 'https://api.example.com',

    // JWT ID - Unique identifier for the token
    'jti' => uniqid('', true),

    // Custom claims
    'role' => 'admin',
    'permissions' => ['read', 'write'],
];

$token = $encoder->stringify($claims);
```

## API Reference

### Encoder Class

#### Constructor

```php
public function __construct(
    #[\SensitiveParameter]
    string $key,
    array|Algorithm|string $algorithms = [Algorithm::HS256, Algorithm::HS384, Algorithm::HS512],
    ?ClockInterface $clock = null
)
```

- `$key` - Secret key for HMAC signing (marked as sensitive)
- `$algorithms` - Allowed algorithms (defaults to all supported)
- `$clock` - Optional PSR-20 Clock implementation for time-based claim validation. If not provided, uses system time via `time()`. Useful for testing with controllable time.

#### Methods

##### `stringify()`

Creates a JWT token from claims.

```php
public function stringify(
    array|object $claims,
    Algorithm|string $alg = Algorithm::HS256,
    string $typ = 'JWT'
): string
```

**Parameters:**

- `$claims` - Claims to encode (array or object)
- `$alg` - Algorithm to use (enum or string)
- `$typ` - Token type (defaults to 'JWT')

**Returns:** JWT token string

**Throws:** `UnsupportedAlgorithmException` if algorithm is not allowed/supported

##### `parse()`

Parses and validates a JWT token.

```php
public function parse(string $token, bool $assert = true): object
```

**Parameters:**

- `$token` - JWT token string
- `$assert` - Whether to validate claims (defaults to true)

**Returns:** Decoded claims object

**Throws:**

- `InvalidTokenException` - Token is malformed or invalid
- `InvalidSignatureException` - Signature verification failed
- `TokenExpiredException` - Token has expired (if `$assert` is true)

##### `assert()`

Validates JWT claims.

```php
public function assert(object $claims): void
```

**Parameters:**

- `$claims` - Claims object to validate

**Throws:**

- `TokenExpiredException` - Token expired
- `InvalidTokenException` - Invalid claims (iat in future, nbf not met)

##### `encode()` / `decode()`

Static utility methods for base64url encoding/decoding.

```php
public static function encode(string $payload): string
public static function decode(string $payload): string
```

### Algorithm Enum

```php
enum Algorithm: string
{
    case HS256 = 'HS256';
    case HS384 = 'HS384';
    case HS512 = 'HS512';

    public function hashMethod(): string;
    public static function values(): array;
}
```

## Exception Handling

The library provides specific exception types for different error scenarios:

- **`InvalidTokenException`** - Token is malformed, invalid encoding, or has invalid claims
- **`TokenExpiredException`** - Token has expired (exp claim)
- **`InvalidSignatureException`** - Signature verification failed
- **`UnsupportedAlgorithmException`** - Algorithm is not allowed or not supported

All exceptions extend standard PHP exceptions, so you can catch them specifically or use a general exception handler.

## Security Considerations

1. **Secret Key Management**: Always use strong, randomly generated secret keys
2. **Key Storage**: Never commit secret keys to version control
3. **HTTPS**: Always transmit tokens over HTTPS
4. **Algorithm Restriction**: Restrict allowed algorithms to only what you need
5. **Token Expiration**: Always set appropriate expiration times
6. **Sensitive Data**: Don't store sensitive information in token payload (it's base64 encoded, not encrypted)

## Requirements

- PHP >= 8.2
- Composer (for installation)

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## Support

For issues, questions, or contributions, please visit the [GitHub repository](https://github.com/vaibhavpandeyvpz/jweety).
