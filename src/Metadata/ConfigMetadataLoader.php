<?php

declare(strict_types=1);

namespace Kenny1911\SymfonyHttpException\Metadata;

use Kenny1911\SymfonyHttpException\Attribute\BaseHttpException;
use Kenny1911\SymfonyHttpException\Attribute\HttpException;
use Kenny1911\SymfonyHttpException\ExpressionLanguage\Expression;

/**
 * @api
 */
final class ConfigMetadataLoader implements MetadataLoader
{
    private const EXPR_REGEX = '/^expr\((?P<expr>.+)\)$/';

    private ArrayMetadataLoader $loader;

    /**
     * @param array<class-string<\Throwable>, array{
     *     statusCode: int<400, 599>,
     *     message?: string,
     *     parameters?: array<non-empty-string, non-empty-string>,
     *     translationDomain?: string,
     *     headers?: array<non-empty-string, non-empty-string>
     * }> $config
     */
    public function __construct(array $config)
    {
        $metadata = array_map(
            static fn($classConfig) => new HttpException(
                statusCode: $classConfig['statusCode'],
                message: $classConfig['message'] ?? null,
                parameters: array_map(self::prepareParameter(...), $classConfig['parameters'] ?? []),
                translationDomain: $classConfig['translationDomain'] ?? 'http_message',
                headers: array_map(self::prepareParameter(...), $classConfig['headers'] ?? []),
            ),
            $config,
        );

        $this->loader = new ArrayMetadataLoader($metadata);
    }

    public function load(string $class): ?BaseHttpException
    {
        return $this->loader->load($class);
    }

    /**
     * @param non-empty-string $parameter
     * @return Expression|non-empty-string
     */
    private static function prepareParameter(string $parameter): Expression|string
    {
        if (preg_match(self::EXPR_REGEX, $parameter, $matches)) {
            return new Expression($matches['expr']);
        }

        return $parameter;
    }
}
