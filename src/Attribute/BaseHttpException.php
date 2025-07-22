<?php

declare(strict_types=1);

namespace Kenny1911\SymfonyHttpException\Attribute;

/**
 * @api
 */
abstract class BaseHttpException
{
    /**
     * @param int<400, 599> $statusCode
     * @param array<non-empty-string, non-empty-string> $translationParameters
     * @param array<non-empty-string, non-empty-string> $headers
     */
    public function __construct(
        public int $statusCode,
        public ?string $message = null,
        public array $translationParameters = [],
        public ?string $translationDomain = 'http_message',
        public array $headers = [],
    ) {}
}
