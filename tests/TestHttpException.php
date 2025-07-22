<?php

declare(strict_types=1);

namespace Kenny1911\SymfonyHttpException\Tests;

use Kenny1911\SymfonyHttpException\Attribute\HttpException;
use Symfony\Component\HttpFoundation\Response;

/**
 * @internal
 * @psalm-internal Kenny1911\SymfonyHttpException\Tests
 */
#[HttpException(
    statusCode: Response::HTTP_NOT_FOUND,
    message: 'Some message with simple param = "{ simple_param }" and translated param = "{ translated_param }"',
    translationParameters: [
        '{ simple_param }' => 'e.simpleParam()',
        '{ translated_param }' => 'translator.trans(e.translatedParam())',
    ],
    translationDomain: 'test',
    headers: ['X-Foo' => 'foo'],
)]
final class TestHttpException extends \Exception
{
    public function simpleParam(): int
    {
        return 34;
    }

    public function translatedParam(): string
    {
        return 'orig value';
    }
}
