<?php

declare(strict_types=1);

namespace Kenny1911\SymfonyHttpException\Attribute;

use Kenny1911\SymfonyHttpException\ExpressionLanguage\Expression;

/**
 * @api
 */
abstract class BaseHttpException
{
    /**
     * @param int<400, 599> $statusCode
     * @param array<non-empty-string, non-empty-string|Expression> $parameters
     * @param array<non-empty-string, non-empty-string|Expression> $headers
     */
    public function __construct(
        public int $statusCode,
        public ?string $message = null,
        public array $parameters = [],
        public string $translationDomain = 'http_message',
        public array $headers = [],
    ) {}
}
