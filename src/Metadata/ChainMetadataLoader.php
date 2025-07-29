<?php

declare(strict_types=1);

namespace Kenny1911\SymfonyHttpException\Metadata;

use Kenny1911\SymfonyHttpException\Attribute\BaseHttpException;

/**
 * @api
 */
final class ChainMetadataLoader implements MetadataLoader
{
    /**
     * @param iterable<MetadataLoader> $loaders
     */
    public function __construct(
        private readonly iterable $loaders,
    ) {}

    public function load(string $class): ?BaseHttpException
    {
        foreach ($this->loaders as $loader) {
            $httpException = $loader->load($class);

            if ($httpException instanceof BaseHttpException) {
                return $httpException;
            }
        }

        return null;
    }
}
