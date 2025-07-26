# Upgrade to `0.2.0`

1. Wrap all parameters to `Kenny1911\SymfonyHttpException\ExpressionLanguage\Expression`.
2. Rename named parameter `translationParameters` to `parameters`.
3. Now You can use `Expression` for headers.

**Before:**

```php
use Kenny1911\SymfonyHttpException\Attribute\HttpException;

#[HttpException(
    translationParameters: [
        '{ username }' => 'e.username',
    ],
    headers: [
        'X-Username' => '', // Expressions not supported
    ],
)]
final class UserIsBlockedException extends Exception {}
```

**After:**

```php
use Kenny1911\SymfonyHttpException\Attribute\HttpException;
use Kenny1911\SymfonyHttpException\ExpressionLanguage\Expression;

#[HttpException(
    parameters: [
        '{ username }' => new Expression('e.username'),
    ],
    headers: [
        'X-Username' => new Expression('e.username'),
    ],
)]
final class UserIsBlockedException extends Exception {}
```
