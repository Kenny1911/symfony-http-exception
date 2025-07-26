# Symfony HttpException

\[ [English](./README.md) | Русский \]

Библиотека `kenny1911/symfony-http-exception` предоставляет удобный способ преобразования исключений PHP в
HTTP-исключения Symfony с поддержкой кодов состояния, сообщений, заголовков и возможностей перевода.

## Основные возможности

- Преобразование любых исключений в `HttpException` через атрибуты
- Поддержка HTTP-кодов, сообщений, заголовков и параметров перевода
- Встроенные алиасы для распространённых HTTP-ошибок
- Поддержка системы перевода Symfony
- Использование Symfony ExpressionLanguage для динамических значений

## Установка

Установите через Composer:

```bash
composer require kenny1911/symfony-http-exception
```

Для работы библиотеки необходимо зарегистрировать подписчик событий:

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

> Аргументы `$translator` и `$expressionLanguage` не обязательны

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