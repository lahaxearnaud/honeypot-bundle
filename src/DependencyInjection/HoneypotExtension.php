<?php

namespace Alahaxe\HoneypotBundle\DependencyInjection;

use Alahaxe\HoneypotBundle\Enums\CloudflareBlockMode;
use Alahaxe\HoneypotBundle\Enums\Policy;
use Alahaxe\HoneypotBundle\Services\CounterMeasureManager;
use Alahaxe\HoneypotBundle\Services\CounterMeasures\CloudflareCounterMeasure;
use Alahaxe\HoneypotBundle\Services\CounterMeasures\DebugCounterMeasure;
use Alahaxe\HoneypotBundle\Services\CounterMeasures\LocalLockCounterMeasure;
use Alahaxe\HoneypotBundle\Services\CounterMeasures\LoggerCounterMeasure;
use Alahaxe\HoneypotBundle\Services\UrlDetectorService;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class HoneypotExtension extends Extension
{
    /**
     * @phpstan-ignore-next-line
     */
    public function load(array $configs, ContainerBuilder $container):void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $config['counterMeasures'] ??= [];
        foreach ($config['counterMeasures'] as $class) {
            if (!$container->hasDefinition($class)) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Honeypot counter measure service %s does not exists',
                        $class
                    )
                );
            }
        }

        $patterns = file($config['patternsFile'], FILE_IGNORE_NEW_LINES);
        if (is_bool($patterns)) {
            throw new \InvalidArgumentException(
                sprintf(
                    "File %s can't be read",
                    $config['patternsFile']
                )
            );
        }

        $patterns = array_filter($patterns, fn ($line) => !empty($line) && !str_starts_with($line, '#'));


        $definition = $container->getDefinition(UrlDetectorService::class);
        $definition->setArgument('$honeypotUrlPatterns', $patterns);

        /** @var string[] $policies */
        $policies = $config['policies'] ?? [Policy::POLICY_LOG->value];

        /** @var Policy[] $policies */
        $policies = array_map(fn (string $policy): Policy => Policy::from($policy), $policies);

        if (
            in_array(Policy::POLICY_CLOUDFLARE_LOCK, $policies, true)
            && (
                empty($config['cloudflare']['zone'] ?? '')
                || empty($config['cloudflare']['email'] ?? '')
                || empty($config['cloudflare']['token'] ?? '')
            )
        ) {
            throw new \InvalidArgumentException(
                'You need to provide cloudflareZone, cloudflareEmail, cloudflareToken configuration if you activate a cloudflare policy.'
            );
        }

        $counterMeasures = $config['counterMeasures'] ?? [];

        $definition = $container->getDefinition(CloudflareCounterMeasure::class);
        $definition->setArgument('$cloudflareEmail', $config['cloudflare']['email'] ?? '');
        $definition->setArgument('$cloudflareToken', $config['cloudflare']['token'] ?? '');
        $definition->setArgument('$cloudflareApiEndpoint', $config['cloudflare']['endpoint'] ?? '');
        $definition->setArgument(
            '$mode',
            !empty($config['cloudflare']['mode']) ?
                CloudflareBlockMode::from($config['cloudflare']['mode'])
                : CloudflareBlockMode::MODE_JS_CHALLENGE
        );

        $definition = $container->getDefinition(LocalLockCounterMeasure::class);
        $definition->setArgument('$lockTtl', $config['lockTtl']);

        $counterMeasures[] = LoggerCounterMeasure::class;

        if (
            in_array(Policy::POLICY_DEBUG, $policies, true)
        ) {
            $counterMeasures[] = DebugCounterMeasure::class;
        }

        if (
            in_array(Policy::POLICY_LOCAL_LOCK, $policies, true)
        ) {
            $counterMeasures[] = LocalLockCounterMeasure::class;
        }

        if (in_array(Policy::POLICY_CLOUDFLARE_LOCK, $policies, true)) {
            $counterMeasures[] = CloudflareCounterMeasure::class;
        }

        $definition = $container->getDefinition(CounterMeasureManager::class);
        $definition->setArgument('$enabledCounterMeasures', $counterMeasures);
        $definition->setArgument('$ipWhitelist', $config['ipWhitelist'] ?? []);
    }
}
