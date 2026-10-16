<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Kenny1911\SymfonyHttpException\ErrorListener;
use Kenny1911\SymfonyHttpException\Metadata\AttributeMetadataLoader;
use Kenny1911\SymfonyHttpException\Metadata\ChainMetadataLoader;
use Kenny1911\SymfonyHttpException\Metadata\ConfigMetadataLoader;
use Kenny1911\SymfonyHttpException\Translation\SimpleTranslator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

return static function (ContainerConfigurator $container, ContainerBuilder $builder): void {
    $services = $container->services();

    $loaders = [];

    if (true === $builder->getParameter('kenny1911.symfony_http_exception.enable_attributes')) {
        $services->set(AttributeMetadataLoader::class);
        $loaders[] = service(AttributeMetadataLoader::class);
    }

    if ([] !== $builder->getParameter('kenny1911.symfony_http_exception.exceptions')) {
        $services->set(ConfigMetadataLoader::class)
            ->args([param('kenny1911.symfony_http_exception.exceptions')]);
        $loaders[] = service(ConfigMetadataLoader::class);
    }

    $services->set(ChainMetadataLoader::class)
        ->args([$loaders]);

    if (class_exists(ExpressionLanguage::class)) {
        $services->set('kenny1911.symfony_http_exception.expression_language', ExpressionLanguage::class);
        $expressionLanguage = service('kenny1911.symfony_http_exception.expression_language');
    } else {
        $expressionLanguage = null;
    }

    $services->set(ErrorListener::class)
        ->args([
            $builder->has('translator') ? service('translator') : inline_service(SimpleTranslator::class),
            $expressionLanguage,
            service(ChainMetadataLoader::class),
        ])
        ->tag('kernel.event_subscriber');
};
