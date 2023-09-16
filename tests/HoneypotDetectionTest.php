<?php

namespace Alahaxe\HoneypotBundle\Tests;

use Alahaxe\HoneypotBundle\Services\CounterMeasureManager;
use Alahaxe\HoneypotBundle\Services\CounterMeasures\DebugCounterMeasure;
use Alahaxe\HoneypotBundle\Services\CounterMeasures\LocalLockCounterMeasure;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class HoneypotDetectionTest extends WebTestCase
{
    public function testValid404(): void
    {
        $client = static::createClient();
        $client->request('GET', '/foo');
        $this->assertResponseStatusCodeSame(404);
        $this->assertNull(DebugCounterMeasure::$lastHoneyPotDetected);
    }

    public static function honeypotUrlProvider(): array
    {
        return [
            ['/phpmyadmin'],
            ['/phpmyadmin/foo'],
            ['/foo/phpmyadmin'],
            ['/foo/phpmyadmin/foo']
        ];
    }

    /**
     * @dataProvider honeypotUrlProvider
     */
    public function testSimpleHoneypot(string $url): void
    {
        $client = static::createClient();

        /** @var LocalLockCounterMeasure $counterMeasure */
        $counterMeasure = static::getContainer()->get(LocalLockCounterMeasure::class);
        $counterMeasure->unlockIp('127.0.0.1');

        $client->request('GET', $url);
        $this->assertResponseStatusCodeSame(404);
        $this->assertNotNull(DebugCounterMeasure::$lastHoneyPotDetected);
    }


    public function testLocalLock(): void
    {
        $client = static::createClient();

        /** @var LocalLockCounterMeasure $counterMeasure */
        $counterMeasure = static::getContainer()->get(LocalLockCounterMeasure::class);
        $counterMeasure->unlockIp('127.0.0.1');

        /** @var CounterMeasureManager $manager */
        $manager = static::getContainer()->get(CounterMeasureManager::class);
        $manager->setEnabledCounterMeasures([LocalLockCounterMeasure::class]);

        $client->request('GET', '/phpmyadmin');
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);

        $client->request('GET', '/foo');
        $client->getResponse()->getStatusCode();

        $this->assertResponseStatusCodeSame(Response::HTTP_LOCKED);
    }
}
