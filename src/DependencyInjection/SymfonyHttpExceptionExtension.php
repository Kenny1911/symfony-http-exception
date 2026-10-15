<?php

declare(strict_types=1);

namespace Kenny1911\SymfonyHttpException\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

/**
 * @api
 */
final class SymfonyHttpExceptionExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        assert(is_bool($config['enable_attributes']));
        assert(is_array($config['exceptions']));

        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.php');

        $container->setParameter('kenny1911.symfony_http_exception.enable_attributes', $config['enable_attributes']);
        $container->setParameter('kenny1911.symfony_http_exception.exceptions', $config['exceptions']);
    }
}
