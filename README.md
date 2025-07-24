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
use Symfony\Component\HttpFoundation\Response;  

#[HttpException(  
    statusCode: Response::HTTP_FORBIDDEN,  
    message: 'User { username } is blocked. Reason: { reason }.',  
    translationParameters: [  
        '{ username }' => 'e.username',  
        '{ reason }' => 'translator.trans(e.reason)',  
    ],  
    translationDomain: 'message',  
    headers: ['X-Error-Code' => '1024'],  
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