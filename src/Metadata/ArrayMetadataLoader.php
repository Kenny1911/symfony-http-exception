<?php

declare(strict_types=1);

namespace Kenny1911\SymfonyHttpException\Metadata;

use Kenny1911\SymfonyHttpException\Attribute\BaseHttpException;

/**
 * @api
 */
final class ArrayMetadataLoader implements MetadataLoader
{
    /**
     * @param array<class-string<\Throwable>, BaseHttpException> $metadata
     */
    public function __construct(
        private readonly array $metadata,
    ) {}

    public function load(string $class): ?BaseHttpException
    {
        return $this->metadata[$class] ?? null;
    }
}
