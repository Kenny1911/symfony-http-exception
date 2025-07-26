<?php

declare(strict_types=1);

namespace Kenny1911\SymfonyHttpException\Attribute;

use Kenny1911\SymfonyHttpException\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Response;

/**
 * @api
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class NotFoundHttpException extends BaseHttpException
{
    /**
     * @param array<non-empty-string, non-empty-string|Expression> $translationParameters
     * @param array<non-empty-string, non-empty-string|Expression> $headers
     */
    public function __construct(
        ?string $message = null,
        array $translationParameters = [],
        string $translationDomain = 'http_message',
        array $headers = [],
    ) {
        parent::__construct(Response::HTTP_NOT_FOUND, $message, $translationParameters, $translationDomain, $headers);
    }
}
