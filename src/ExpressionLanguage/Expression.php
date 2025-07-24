<?php

declare(strict_types=1);

namespace Kenny1911\SymfonyHttpException\ExpressionLanguage;

/**
 * @api
 */
final class Expression implements \Stringable
{
    public function __construct(
        public readonly string $expression,
    ) {}

    public function __toString(): string
    {
        return $this->expression;
    }
}
