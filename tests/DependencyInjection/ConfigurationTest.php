<?php

declare(strict_types=1);

namespace Kenny1911\SymfonyHttpException\Tests\DependencyInjection;

use Kenny1911\SymfonyHttpException\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;

/**
 * @internal
 * @psalm-internal Kenny1911\SymfonyHttpException\Tests\DependencyInjection
 */
final class ConfigurationTest extends TestCase
{
    public function testDefaultValues(): void
    {
        self::assertSame(
            [
                'enable_attributes' => true,
                'exceptions' => [],
            ],
            self::process([]),
        );
    }

    public function testWithExceptions(): void
    {
        self::assertSame(
            [
                'enable_attributes' => false,
                'exceptions' => [
                    \Exception::class => [
                        'statusCode' => 401,
                        'message' => 'Not authorized',
                        'translationDomain' => 'messages',
                        'parameters' => [],
                        'headers' => [],
                    ],
                ],
            ],
            self::process([
                'enable_attributes' => false,
                'exceptions' => [
                    \Exception::class => [
                        'statusCode' => 401,
                        'message' => 'Not authorized',
                        'translationDomain' => 'messages',
                    ],
                ],
            ]),
        );
    }

    public function testStatusCodeIsRequired(): void
    {
        self::expectException(InvalidConfigurationException::class);

        self::process([
            'exceptions' => [
                \Exception::class => [
                    'message' => 'No status code',
                ],
            ],
        ]);
    }

    public function testStatusCodeRange(): void
    {
        self::expectException(InvalidConfigurationException::class);

        self::process([
            'exceptions' => [
                \Exception::class => [
                    'statusCode' => 200,
                ],
            ],
        ]);
    }

    private static function process(array $config): array
    {
        return (new Processor())->processConfiguration(new Configuration(), [$config]);
    }
}
