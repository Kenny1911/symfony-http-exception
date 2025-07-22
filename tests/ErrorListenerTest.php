<?php

declare(strict_types=1);

namespace Kenny1911\SymfonyHttpException\Tests;

use Kenny1911\SymfonyHttpException\ErrorListener;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @api
 */
final class ErrorListenerTest extends TestCase
{
    /**
     * @throws \ReflectionException
     */
    #[TestWith([
        new TestHttpException('Orig message'),
        true,
        true,
        'Trans(test): Some message with simple param = "34" and translated param = "Trans(test): orig value"',
        404,
        ['X-Foo' => 'foo'],
    ])]
    #[TestWith([
        new TestHttpException('Orig message'),
        true,
        false,
        'Trans(test): Some message with simple param = "{ simple_param }" and translated param = "{ translated_param }"',
        404,
        ['X-Foo' => 'foo'],
    ])]
    #[TestWith([
        new TestHttpException('Orig message'),
        false,
        false,
        'Some message with simple param = "{ simple_param }" and translated param = "{ translated_param }"',
        404,
        ['X-Foo' => 'foo'],
    ])]
    #[TestWith([
        new TestHttpExceptionWithoutParams('Orig message'),
        true,
        true,
        'Trans(http_message): Orig message',
        400,
        [],
    ])]
    #[TestWith([
        new TestHttpExceptionWithoutParams('Orig message'),
        false,
        false,
        'Orig message',
        400,
        [],
    ])]
    #[TestWith([
        new TestNotFoundHttpException('Orig message'),
        true,
        true,
        'Trans(http_message): Orig message',
        404,
        [],
    ])]
    #[TestWith([
        new TestAccessDeniedHttpException('Orig message'),
        true,
        true,
        'Trans(http_message): Orig message',
        403,
        [],
    ])]
    #[TestWith([
        new TestBadRequestHttpException('Orig message'),
        true,
        true,
        'Trans(http_message): Orig message',
        400,
        [],
    ])]
    public function test(
        \Throwable $throwable,
        bool $supportTranslation,
        bool $supportExpressionLanguage,
        string $expectedMessage,
        int $expectedStatusCode,
        array $expectedHeaders,
    ): void {
        $listener = new ErrorListener(
            translator: $supportTranslation ? self::createTranslator() : null,
            expressionLanguage: $supportExpressionLanguage ? new ExpressionLanguage() : null,
        );

        $event = (new \ReflectionClass(ExceptionEvent::class))->newInstanceWithoutConstructor();
        $event->setThrowable($throwable);
        $listener->onKernelException($event);

        $actualThrowable = $event->getThrowable();
        self::assertInstanceOf(HttpException::class, $actualThrowable);
        self::assertSame($expectedMessage, $actualThrowable->getMessage());
        self::assertSame($expectedStatusCode, $actualThrowable->getStatusCode());
        self::assertSame($expectedHeaders, $actualThrowable->getHeaders());
    }

    private static function createTranslator(): TranslatorInterface
    {
        return new class implements TranslatorInterface {
            #[\Override]
            public function trans(string $id, array $parameters = [], ?string $domain = null, ?string $locale = null): string
            {
                return \sprintf('Trans(%s): %s', (string) $domain, strtr($id, $parameters));
            }

            #[\Override]
            public function getLocale(): string
            {
                return '';
            }
        };
    }
}
