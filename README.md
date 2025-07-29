# Symfony HttpException

\[ English | [Русский](./README-RU.md) \]

The `kenny1911/symfony-http-exception` library provides a convenient way to convert PHP exceptions into Symfony HTTP exceptions with support for status codes, messages, headers, and translation capabilities.

## Key Features

- Convert any exception to `HttpException` using attributes
- Support for HTTP status codes, messages, headers, and translation parameters
- Built-in aliases for common HTTP errors
- Symfony Translation component integration
- Dynamic values using Symfony ExpressionLanguage

## Installation

Install via Composer:

```bash
composer require kenny1911/symfony-http-exception
```

Register the event subscriber in your Symfony configuration:

```yaml
# config/services.yaml
services:
    Kenny1911\SymfonyHttpException\ErrorListener:
        arguments:
          - '@translator'
          - !service { class: Symfony\Component\ExpressionLanguage\ExpressionLanguage }
        tags:
            - { name: kernel.event_subscriber }
```

> Arguments `$translator` and `$expressionLanguage` is not required

As the third argument `$metadataLoader`, you can pass the instance of `Kenny1911\SymfonyHttpException\Metadata\MetadataLoader`.
The default value is `AttributeMetadataLoader`. Apart from this, there is also:

- `ArrayMetadataLoader` - use map exception class to `BaseHttpException` instance.
- `ConfigMetadataLoader` - use associative array for describe configuration.
- `ChainMetadataLoader` - combine multiply `MetadataLoader` instances.

Эти загрузчики могут использоваться для описания сторонних классов исключения.

Example of advanced configuration:

```yaml
# config/services.yaml
services:
    Kenny1911\SymfonyHttpException\ErrorListener:
        arguments:
          - '@translator'
          - !service { class: Symfony\Component\ExpressionLanguage\ExpressionLanguage }
          - !service
            class: Kenny1911\SymfonyHttpException\Metadata\ChainMetadataLoader
            arguments:
              - !service { class: Kenny1911\SymfonyHttpException\Metadata\ArrayMetadataLoader }
              - !service
                class: Kenny1911\SymfonyHttpException\Metadata\ConfigMetadataLoader
                arguments:
                  - App\Exceptions\SomeException:
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
        tags:
            - { name: kernel.event_subscriber }
```

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