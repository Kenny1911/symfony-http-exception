<?php

declare(strict_types=1);

namespace Kenny1911\SymfonyHttpException\Metadata;

use Kenny1911\SymfonyHttpException\Attribute\BaseHttpException;

/**
 * @api
 */
final class AttributeMetadataLoader implements MetadataLoader
{
    /**
     * @throws \ReflectionException
     */
    public function load(string $class): ?BaseHttpException
    {
        $refClass = new \ReflectionClass($class);

        do {
            $attribute = $refClass->getAttributes(BaseHttpException::class, \ReflectionAttribute::IS_INSTANCEOF)[0] ?? null;

            if (null === $attribute) {
                continue;
            }

            return $attribute->newInstance();
        } while ($refClass = $refClass->getParentClass());

        return null;
    }
}
