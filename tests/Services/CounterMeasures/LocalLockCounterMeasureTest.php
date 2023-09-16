<?php

namespace Alahaxe\HoneypotBundle\Tests\Services\CounterMeasures;

use Alahaxe\HoneypotBundle\Services\CounterMeasures\LocalLockCounterMeasure;
use Alahaxe\HoneypotBundle\Services\VisitorFingerPrintService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\HttpFoundation\Request;

class LocalLockCounterMeasureTest extends KernelTestCase
{
    public function testTriggered(): void
    {
        $cache = new ArrayAdapter();
        $counterMeasure = new LocalLockCounterMeasure(
            self::getContainer()->get(VisitorFingerPrintService::class),
            $cache,
            60,
        );


        $honeyPot = '/phpmyadmin';
        $request = Request::create('/phpmyadmin/foo', Request::METHOD_GET);

        $counterMeasure->react($request, $honeyPot);

        $this->assertNotEmpty($cache->getValues());
    }
}
