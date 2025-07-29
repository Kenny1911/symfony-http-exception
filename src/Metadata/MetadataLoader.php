<?php

declare(strict_types=1);

namespace Kenny1911\SymfonyHttpException\Metadata;

use Kenny1911\SymfonyHttpException\Attribute\BaseHttpException;

/**
 * @api
 */
interface MetadataLoader
{
    /**
     * @param class-string<\Throwable> $class
     */
    public function load(string $class): ?BaseHttpException;
}
