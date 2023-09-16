<?php

namespace Alahaxe\HoneypotBundle\Tests\Services\CounterMeasures;

use Alahaxe\HoneypotBundle\Enums\CloudflareBlockMode;
use Alahaxe\HoneypotBundle\Services\CounterMeasures\CloudflareCounterMeasure;
use Alahaxe\HoneypotBundle\Services\VisitorFingerPrintService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Request;

class CloudflareCounterMeasureTest extends KernelTestCase
{
    public function testTriggered(): void
    {
        self::bootKernel();

        $endpoint = 'https://httpbin.org/post';
        $callback = function ($method, $url, $options) use ($endpoint) : MockResponse {
            $this->assertEquals(Request::METHOD_POST, $method);
            $this->assertEquals($endpoint, $url);
            $this->assertIsArray($options);
            $this->assertIsArray($options['normalized_headers']);
            $this->assertArrayHasKey('x-auth-email', $options['normalized_headers']);
            $this->assertArrayHasKey('x-auth-key', $options['normalized_headers']);
            $options['body'] = \json_decode($options['body'], true);
            $this->assertIsArray($options['body']);

            return new MockResponse('{}');
        };

        $client = new MockHttpClient($callback);

        $cloudflareCounterMeasure = new CloudflareCounterMeasure(
            self::getContainer()->get(VisitorFingerPrintService::class),
            $client,
            'dummt@test.fr',
            'dummy-token',
            CloudflareBlockMode::MODE_CHALLENGE,
            $endpoint
        );

        $honeyPot = '/phpmyadmin';
        $request = Request::create('/phpmyadmin/foo', Request::METHOD_GET);

        $cloudflareCounterMeasure->react($request, $honeyPot);
    }
}
