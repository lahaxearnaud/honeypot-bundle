<?php

namespace Alahaxe\HoneypotBundle\DependencyInjection;

use Alahaxe\HoneypotBundle\Service\Reporter\ReporterFactory;
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

        $definition = $container->getDefinition('alahaxe_HoneypotBundle.report_factory');
        $definition->replaceArgument('$httpType', ($config['http']['format'] ?? 'minimal'));
        $definition->replaceArgument('$cliType', ($config['cli']['format'] ?? 'full'));
    }
}
