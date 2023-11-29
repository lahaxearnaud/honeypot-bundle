<?php

namespace Alahaxe\HoneypotBundle\DependencyInjection;

use Alahaxe\HoneypotBundle\Enums\CloudflareBlockMode;
use Alahaxe\HoneypotBundle\Enums\Policy;
use Alahaxe\HoneypotBundle\Services\CounterMeasures\CloudflareCounterMeasure;
use Alahaxe\HoneypotBundle\Services\LockedResponseGenerator\SimpleResponseGenerator;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('honeypot');

        /** @phpstan-ignore-next-line  */
        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('patternsFile')
                    ->defaultValue(__DIR__ . '/../../Resources/patterns.txt')
                ->end()

                ->arrayNode('counterMeasures')
                    ->scalarPrototype()->end()
                    ->beforeNormalization()->castToArray()->end()
                ->end()

                ->arrayNode('ipWhitelist')
                    ->scalarPrototype()->end()
                    ->beforeNormalization()->castToArray()->end()
                ->end()

                ->arrayNode('policies')
                    ->enumPrototype()->values(
                        array_map(fn (Policy $policy): string => $policy->value, Policy::cases())
                    )->end()
                    ->beforeNormalization()->castToArray()->end()
                ->end()

                ->arrayNode('localLock')
                     ->children()
                        ->integerNode('lockTtl')
                            ->defaultValue(60)
                        ->end()
                        ->scalarNode('renderService')
                            ->defaultValue(SimpleResponseGenerator::class)
                        ->end()
                        ->scalarNode('twigTemplate')
                            ->defaultValue('@HoneypotBundle/lock.html.twig')
                        ->end()
                    ->end()
                ->end()

                ->arrayNode('cloudflare')
                    ->children()
                         ->scalarNode('email')->end()
                         ->scalarNode('token')->end()
                         ->scalarNode('endpoint')
                            ->defaultValue(CloudflareCounterMeasure::FIREWALL_API_ENDPOINT)
                         ->end()
                         ->enumNode('mode')
                            ->values(
                                array_map(fn (CloudflareBlockMode $policy): string => $policy->value, CloudflareBlockMode::cases())
                            )
                         ->end()
                    ->end()
                ->end()
        ;

        return $treeBuilder;
    }
}
