<?php

namespace Alahaxe\HoneypotBundle\Tests\Services\CounterMeasures;

use Alahaxe\HoneypotBundle\Services\CounterMeasures\LoggerCounterMeasure;
use Psr\Log\Test\TestLogger;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;

class LoggerCounterMeasureTest extends KernelTestCase
{
    public function testTriggered(): void
    {
        $logger = new TestLogger();

        $counterMeasure = new LoggerCounterMeasure();
        $counterMeasure->setLogger($logger);

        $honeyPot = '/phpmyadmin';
        $request = Request::create('/phpmyadmin/foo', Request::METHOD_GET);

        $counterMeasure->react($request, $honeyPot);

        $this->assertNotEmpty(
            $logger->records
        );
    }
}
