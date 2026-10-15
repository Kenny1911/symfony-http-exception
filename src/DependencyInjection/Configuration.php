<?php

declare(strict_types=1);

namespace Kenny1911\SymfonyHttpException\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @internal
 * @psalm-internal Kenny1911\SymfonyHttpException\DependencyInjection
 *
 * @psalm-type Config = array{
 *     enable_attributes: bool,
 *     exceptions: array<class-string<\Throwable>, array{
 *         statusCode: int<400, 599>,
 *         message?: string,
 *         parameters?: array<non-empty-string, non-empty-string>,
 *         translationDomain?: string,
 *         headers?: array<non-empty-string, non-empty-string>
 *     }>
 * }
 */
final class Configuration implements ConfigurationInterface
{
    /**
     * @psalm-suppress MixedMethodCall,UndefinedMethod,PossiblyNullReference,UndefinedInterfaceMethod
     */
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
