<?php

namespace Gigablah\PersonaProviderBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('gb_persona_provider');

        $rootNode
            ->children()
                ->scalarNode('host')->defaultNull()->end()
                ->scalarNode('key_path')->defaultValue('%kernel.root_dir%/Resources/data')->cannotBeEmpty()->end()
            ->end();

        return $treeBuilder;
    }
}
