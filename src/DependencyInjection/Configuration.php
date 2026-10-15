<?php

declare(strict_types=1);

namespace Kenny1911\SymfonyHttpException\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
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
        assert($rootNode instanceof ArrayNodeDefinition);

        $children = $rootNode->children();

        $enableAttributesNode = $children->booleanNode('enable_attributes');
        $enableAttributesNode->defaultTrue();
        $enableAttributesNode->info('Whether attribute-based metadata loading is enabled.');
        $children = $enableAttributesNode->end();
        assert($children instanceof NodeBuilder);

        $exceptionsNode = $children->arrayNode('exceptions');
        $exceptionsNode->useAttributeAsKey('class');

        $prototype = $exceptionsNode->prototype('array');
        assert($prototype instanceof ArrayNodeDefinition);

        $protoChildren = $prototype->children();

        $statusCodeNode = $protoChildren->integerNode('statusCode');
        $statusCodeNode->min(400);
        $statusCodeNode->max(599);
        $statusCodeNode->isRequired();
        $statusCodeNode->cannotBeEmpty();
        $protoChildren = $statusCodeNode->end();
        assert($protoChildren instanceof NodeBuilder);

        $messageNode = $protoChildren->scalarNode('message');
        $messageNode->defaultNull();
        $protoChildren = $messageNode->end();
        assert($protoChildren instanceof NodeBuilder);

        $parametersNode = $protoChildren->arrayNode('parameters');
        $parametersNode->prototype('variable');
        $protoChildren = $parametersNode->end();
        assert($protoChildren instanceof NodeBuilder);

        $translationDomainNode = $protoChildren->scalarNode('translationDomain');
        $translationDomainNode->defaultValue('http_message');
        $protoChildren = $translationDomainNode->end();
        assert($protoChildren instanceof NodeBuilder);

        $headersNode = $protoChildren->arrayNode('headers');
        $headersNode->prototype('variable');
        $protoChildren = $headersNode->end();
        assert($protoChildren instanceof NodeBuilder);

        $prototype = $protoChildren->end();
        assert($prototype instanceof ArrayNodeDefinition);

        $children = $prototype->end();
        assert($children instanceof NodeBuilder);

        $rootNode = $children->end();
        assert($rootNode instanceof ArrayNodeDefinition);

        return $treeBuilder;
    }
}
