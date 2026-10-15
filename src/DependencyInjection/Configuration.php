<?php

declare(strict_types=1);

namespace Kenny1911\SymfonyHttpException\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @api
 */
final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('symfony_http_exception');

        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->booleanNode('enable_attributes')
                    ->defaultTrue()
                    ->info('Whether attribute-based metadata loading is enabled.')
                ->end()
                ->arrayNode('exceptions')
                    ->useAttributeAsKey('class')
                    ->prototype('array')
                        ->children()
                            ->integerNode('statusCode')
                                ->min(400)
                                ->max(599)
                                ->isRequired()
                                ->cannotBeEmpty()
                                ->end()
                            ->scalarNode('message')
                                ->defaultNull()
                                ->end()
                            ->arrayNode('parameters')
                                ->prototype('variable')
                                ->end()
                            ->end()
                            ->scalarNode('translationDomain')
                                ->defaultValue('http_message')
                                ->end()
                            ->arrayNode('headers')
                                ->prototype('variable')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
