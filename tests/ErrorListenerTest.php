<?php

declare(strict_types=1);

namespace Kenny1911\SymfonyHttpException\Tests;

use Kenny1911\SymfonyHttpException\ErrorListener;
use Kenny1911\SymfonyHttpException\ExpressionLanguage\Expression;
use Kenny1911\SymfonyHttpException\Metadata\ArrayMetadataLoader;
use Kenny1911\SymfonyHttpException\Metadata\AttributeMetadataLoader;
use Kenny1911\SymfonyHttpException\Metadata\ChainMetadataLoader;
use Kenny1911\SymfonyHttpException\Metadata\ConfigMetadataLoader;
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
        'Trans(test): Some message with static param = "Static", expression param = "34" and translated expression param = "Trans(test): orig value"',
        404,
        [
            'X-Static' => 'Static',
            'X-Expression' => '34',
            'X-Translated-Expression' => 'Trans(test): orig value',
        ],
    ])]
    #[TestWith([
        new TestHttpException('Orig message'),
        true,
        false,
        'Trans(test): Some message with static param = "Static", expression param = "e.expressionParam()" and translated expression param = "translator.trans(e.translatedExpressionParam())"',
        404,
        [
            'X-Static' => 'Static',
            'X-Expression' => 'e.expressionParam()',
            'X-Translated-Expression' => 'translator.trans(e.translatedExpressionParam())',
        ],
    ])]
    #[TestWith([
        new TestHttpException('Orig message'),
        false,
        true,
        'Some message with static param = "Static", expression param = "34" and translated expression param = "orig value"',
        404,
        [
            'X-Static' => 'Static',
            'X-Expression' => '34',
            'X-Translated-Expression' => 'orig value',
        ],
    ])]
    #[TestWith([
        new TestHttpException('Orig message'),
        false,
        false,
        'Some message with static param = "Static", expression param = "e.expressionParam()" and translated expression param = "translator.trans(e.translatedExpressionParam())"',
        404,
        [
            'X-Static' => 'Static',
            'X-Expression' => 'e.expressionParam()',
            'X-Translated-Expression' => 'translator.trans(e.translatedExpressionParam())',
        ],
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
    #[TestWith([
        new TestExceptionFromArrayMetadataLoader('Orig message, using array'),
        true,
        true,
        'Trans(test): Not authorized. Orig message, using array',
        401,
        ['X-Exception-Message' => 'Orig message, using array'],
    ])]
    #[TestWith([
        new TestExceptionFromConfigMetadataLoader('Orig message, using array'),
        true,
        true,
        'Trans(test): Not authorized. Orig message, using array',
        401,
        ['X-Exception-Message' => 'Orig message, using array'],
    ])]
    public function test(
        \Throwable $throwable,
        bool $supportTranslation,
        bool $supportExpressionLanguage,
        string $expectedMessage,
        int $expectedStatusCode,
        array $expectedHeaders,
    ): void {
        /** @var TranslatorInterface $defaultTranslator */
        $defaultTranslator = (new \ReflectionClass(ErrorListener::class))
            ->getConstructor()
            ?->getParameters()[0]
            ?->getDefaultValue();

        $listener = new ErrorListener(
            translator: $supportTranslation ? self::createTranslator() : $defaultTranslator,
            expressionLanguage: $supportExpressionLanguage ? new ExpressionLanguage() : null,
            metadataLoader: new ChainMetadataLoader([
                new AttributeMetadataLoader(),
                new ArrayMetadataLoader([
                    TestExceptionFromArrayMetadataLoader::class => new \Kenny1911\SymfonyHttpException\Attribute\HttpException(
                        statusCode: 401,
                        message: 'Not authorized. { exception_message }',
                        parameters: ['{ exception_message }' => new Expression('e.getMessage()')],
                        translationDomain: 'test',
                        headers: ['X-Exception-Message' => new Expression('e.getMessage()')],
                    ),
                ]),
                new ConfigMetadataLoader([
                    TestExceptionFromConfigMetadataLoader::class => [
                        'statusCode' => 401,
                        'message' => 'Not authorized. { exception_message }',
                        'parameters' => ['{ exception_message }' => 'expr(e.getMessage())'],
                        'translationDomain' => 'test',
                        'headers' => ['X-Exception-Message' => 'expr(e.getMessage())'],
                    ],
                ]),
            ]),
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
            public function trans(string $id, array $parameters = [], ?string $domain = null, ?string $locale = null): string
            {
                return \sprintf('Trans(%s): %s', (string) $domain, strtr($id, $parameters));
            }

            public function getLocale(): string
            {
                return '';
            }
        };
    }
}
