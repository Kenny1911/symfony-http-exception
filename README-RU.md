# Symfony HttpException

\[ [English](./README.md) | Русский \]

Библиотека `kenny1911/symfony-http-exception` предоставляет удобный способ преобразования исключений PHP в
HTTP-исключения Symfony с поддержкой кодов состояния, сообщений, заголовков и возможностей перевода.

## Основные возможности

- Преобразование любых исключений в `HttpException` через атрибуты или конфигурацию
- Автоматическая регистрация как Symfony-бандла
- Поддержка HTTP-кодов, сообщений, заголовков и параметров перевода
- Встроенные алиасы для распространённых HTTP-ошибок
- Поддержка системы перевода Symfony
- Использование Symfony ExpressionLanguage для динамических значений

## Установка

Установите через Composer:

```bash
composer require kenny1911/symfony-http-exception
```

Зарегистрируйте бандл в `config/bundles.php`:

```php
return [
    // ...
    Kenny1911\SymfonyHttpException\SymfonyHttpExceptionBundle::class => ['all' => true],
];
```

Настройте бандл в `config/packages/kenny1911_symfony_http_exception.yaml`:

```yaml
kenny1911_symfony_http_exception:
    enable_attributes: true # значение по умолчанию

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

Опции:

- `enable_attributes` — включает загрузку метаданных из атрибутов (по умолчанию `true`).
- `exceptions` — сопоставление FQCN исключения с метаданными HTTP-исключения:
  - `statusCode` — обязательный, `400`–`599`;
  - `message` — по умолчанию используется сообщение исключения;
  - `parameters` — параметры-плейсхолдеры для `message`;
  - `translationDomain` — по умолчанию `http_message`;
  - `headers` — заголовки ответа.

Значения в обёртке `expr(...)` вычисляются через ExpressionLanguage (`e` — исключение, `translator` — транслятор); для
этого требуется `symfony/expression-language`.

Метаданные из конфигурации имеют приоритет над атрибутами для одного и того же класса исключения.

## Использование

### Минимальный пример

```php
use Kenny1911\SymfonyHttpException\Attribute\HttpException;
use Symfony\Component\HttpFoundation\Response;

#[HttpException(statusCode: Response::HTTP_FORBIDDEN)]
final class UserIsBlockedException extends Exception {}
```

### Расширенный пример

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

### Встроенные алиасы

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

## Лицензия

Библиотека распространяется под лицензией MIT.