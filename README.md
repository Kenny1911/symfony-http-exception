# Symfony HttpException

\[ English | [Русский](./README-RU.md) \]

The `kenny1911/symfony-http-exception` library provides a convenient way to convert PHP exceptions into
Symfony HTTP exceptions with support for status codes, messages, headers, and translation capabilities.

## Key Features

- Convert any exception to `HttpException` via attributes or configuration
- Automatic registration as a Symfony bundle
- Support for HTTP status codes, messages, headers, and translation parameters
- Built-in aliases for common HTTP errors
- Symfony Translation component integration
- Using Symfony ExpressionLanguage for dynamic values

## Installation

Install via Composer:

```bash
composer require kenny1911/symfony-http-exception
```

Register the bundle in `config/bundles.php`:

```php
return [
    // ...
    Kenny1911\SymfonyHttpException\SymfonyHttpExceptionBundle::class => ['all' => true],
];
```

Configure the bundle in `config/packages/kenny1911_symfony_http_exception.yaml`:

```yaml
kenny1911_symfony_http_exception:
    enable_attributes: true # default

    exceptions:
        App\Exceptions\SomeException:
            statusCode: 401
            message: 'Not authorized. Error code: { error_code }. { exception_message }'
            parameters:
                '{ error_code }': '10420'
                '{ exception_message }': 'expr(e.getMessage())'
            translationDomain: message
            headers:
                'X-Error-Code': '10420'
                'X-Error-Message': 'expr(e.getMessage())'

        App\Exceptions\OtherException:
            statusCode: 400
```

Options:

- `enable_attributes` — enables loading metadata from attributes (default `true`).
- `exceptions` — maps an exception FQCN to HTTP exception metadata:
  - `statusCode` — required, `400`–`599`;
  - `message` — the exception message is used by default;
  - `parameters` — placeholder parameters for `message`;
  - `translationDomain` — `http_message` by default;
  - `headers` — response headers.

Values wrapped in `expr(...)` are evaluated by ExpressionLanguage (`e` is the exception, `translator` is the translator);
this requires `symfony/expression-language`.

Metadata from configuration takes precedence over attributes for the same exception class.

## Usage

### Basic Example

```php
use Kenny1911\SymfonyHttpException\Attribute\HttpException;
use Symfony\Component\HttpFoundation\Response;

#[HttpException(statusCode: Response::HTTP_FORBIDDEN)]
final class UserIsBlockedException extends Exception {}
```

### Advanced Example

```php
use Kenny1911\SymfonyHttpException\Attribute\HttpException;
use Kenny1911\SymfonyHttpException\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Response;

#[HttpException(
    statusCode: Response::HTTP_FORBIDDEN,
    message: 'User { username } is blocked. Error code: { error_code }. Reason: { reason }.',
    parameters: [
        '{ error_code }' => '1234',
        '{ username }' => new Expression('e.username'),
        '{ reason }' => new Expression('translator.trans(e.reason)'),
    ],
    translationDomain: 'message',
    headers: [
        'X-Error-Code' => '1024',
        'X-Username' => new Expression('e.username'),
        'X-Reason' => new Expression('translator.trans(e.reason)'),
    ],
)]
final class UserIsBlockedException extends Exception
{
    public function __construct(
        public readonly string $username,
        public readonly string $reason,
    ) {}
}
```

### Built-in Aliases

```php
use Kenny1911\SymfonyHttpException\Attribute\AccessDeniedHttpException;

#[AccessDeniedHttpException]
final class UserIsBlockedException extends Exception {}
```

```php
use Kenny1911\SymfonyHttpException\Attribute\NotFoundHttpException;

#[NotFoundHttpException]
final class UserNotFoundException extends Exception {}
```

```php
use Kenny1911\SymfonyHttpException\Attribute\BadRequestHttpException;

#[BadRequestHttpException]
final class InvalidUsernameException extends Exception {}
```

## License

This library is released under the MIT License.
