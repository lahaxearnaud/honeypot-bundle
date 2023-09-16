<?php

namespace Alahaxe\HoneypotBundle\Tests;

use Alahaxe\HoneypotBundle\HoneypotBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\MonologBundle\MonologBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class TestKernel extends Kernel
{
    public function registerBundles():array
    {
        return array(
            new FrameworkBundle(),
            new MonologBundle(),
            new HoneypotBundle(),
        );
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config.yaml');
    }
}
