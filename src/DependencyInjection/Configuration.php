<?php

namespace Alahaxe\HoneypotBundle\DependencyInjection;

use Alahaxe\HoneypotBundle\Enums\CloudflareBlockMode;
use Alahaxe\HoneypotBundle\Enums\Policy;
use Alahaxe\HoneypotBundle\Services\CounterMeasures\CloudflareCounterMeasure;
use Alahaxe\HoneypotBundle\Services\LockedResponseGenerator\SimpleResponseGenerator;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('honeypot');

        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();
        $children = $rootNode->children();

        $children->scalarNode('patternsFile')
            ->defaultValue(__DIR__ . '/../../Resources/patterns.txt')
            ->end();

        $counterMeasures = $children->arrayNode('counterMeasures');
        $counterMeasures->scalarPrototype()->end();
        $counterMeasures->beforeNormalization()->castToArray()->end();

        $ipWhitelist = $children->arrayNode('ipWhitelist');
        $ipWhitelist->scalarPrototype()->end();
        $ipWhitelist->beforeNormalization()->castToArray()->end();

        $policies = $children->arrayNode('policies');
        $policies->enumPrototype()->values(
            array_map(fn (Policy $policy): string => $policy->value, Policy::cases())
        )->end();
        $policies->beforeNormalization()->castToArray()->end();

        $localLock = $children->arrayNode('localLock');
        $localLockChildren = $localLock->children();
        $localLockChildren->integerNode('lockTtl')->defaultValue(60)->end();
        $localLockChildren->scalarNode('renderService')
            ->defaultValue(SimpleResponseGenerator::class)
            ->end();
        $localLockChildren->scalarNode('twigTemplate')
            ->defaultValue('@HoneypotBundle/lock.html.twig')
            ->end();

        $cloudflare = $children->arrayNode('cloudflare');
        $cloudflareChildren = $cloudflare->children();
        $cloudflareChildren->scalarNode('email')->end();
        $cloudflareChildren->scalarNode('token')->end();
        $cloudflareChildren->scalarNode('endpoint')
            ->defaultValue(CloudflareCounterMeasure::FIREWALL_API_ENDPOINT)
            ->end();
        $cloudflareChildren->enumNode('mode')->values(
            array_map(fn (CloudflareBlockMode $policy): string => $policy->value, CloudflareBlockMode::cases())
        )->end();

        return $treeBuilder;
    }
}
