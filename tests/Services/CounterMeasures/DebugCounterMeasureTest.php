<?php

namespace Alahaxe\HoneypotBundle\Tests\Services\CounterMeasures;

use Alahaxe\HoneypotBundle\Services\CounterMeasures\DebugCounterMeasure;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;

class DebugCounterMeasureTest extends KernelTestCase
{
    public function testTriggered(): void
    {
        self::bootKernel();

        $counterMeasure = self::getContainer()->get(DebugCounterMeasure::class);

        $honeyPot = '/phpmyadmin';
        $request = Request::create('/phpmyadmin/foo', Request::METHOD_GET);

        $counterMeasure->react($request, $honeyPot);

        $this->assertNotEmpty(
            DebugCounterMeasure::$lastHoneyPotDetected
        );
    }
}
