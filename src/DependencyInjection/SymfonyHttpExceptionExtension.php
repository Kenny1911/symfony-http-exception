<?php

declare(strict_types=1);

namespace Kenny1911\SymfonyHttpException\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

/**
 * @internal
 * @psalm-internal Kenny1911\SymfonyHttpException
 *
 * @psalm-import-type Config from Configuration
 */
final class SymfonyHttpExceptionExtension extends Extension
{
    public function getAlias(): string
    {
        return 'kenny1911_symfony_http_exception';
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        /** @var Config $config */
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('kenny1911.symfony_http_exception.enable_attributes', $config['enable_attributes']);
        $container->setParameter('kenny1911.symfony_http_exception.exceptions', $config['exceptions']);

        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $loader->load('services.php');
    }
}
