<?php

declare(strict_types=1);

namespace Kenny1911\SymfonyHttpException\Tests;

use Kenny1911\SymfonyHttpException\Attribute\HttpException;
use Kenny1911\SymfonyHttpException\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Response;

/**
 * @internal
 * @psalm-internal Kenny1911\SymfonyHttpException\Tests
 */
#[HttpException(
    statusCode: Response::HTTP_NOT_FOUND,
    message: 'Some message with static param = "{ static_param }", expression param = "{ expression_param }" and translated expression param = "{ translated_expression_param }"',
    translationParameters: [
        '{ static_param }' => 'Static',
        '{ expression_param }' => new Expression('e.expressionParam()'),
        '{ translated_expression_param }' => new Expression('translator.trans(e.translatedExpressionParam())'),
    ],
    translationDomain: 'test',
    headers: [
        'X-Static' => 'Static',
        'X-Expression' => new Expression('e.expressionParam()'),
        'X-Translated-Expression' => new Expression('translator.trans(e.translatedExpressionParam())'),
    ],
)]
final class TestHttpException extends \Exception
{
    public function expressionParam(): int
    {
        return 34;
    }

    public function translatedExpressionParam(): string
    {
        return 'orig value';
    }
}
