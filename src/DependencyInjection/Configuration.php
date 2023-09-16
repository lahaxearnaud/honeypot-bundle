<?php

namespace Alahaxe\HoneypotBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('health_check');
        /** @phpstan-ignore-next-line  */
        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('http')
                    ->children()
                        ->enumNode('format')
                            ->values(['full', 'minimal'])
                            ->defaultValue('minimal')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('cli')
                    ->children()
                        ->enumNode('format')
                        ->values(['full'])
                        ->defaultValue('full')
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
