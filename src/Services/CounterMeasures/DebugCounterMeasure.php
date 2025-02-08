<?php
declare(strict_types=1);

namespace Alahaxe\HoneypotBundle\Services\CounterMeasures;

use Alahaxe\HoneypotBundle\Services\CounterMeasureInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface;

class DebugCounterMeasure implements CounterMeasureInterface, DataCollectorInterface
{
    public static ?string $lastHoneyPotDetected = null;

    public function react(Request $request, string $honeypotPattern): void
    {
        self::$lastHoneyPotDetected = $honeypotPattern;
    }

    public function collect(Request $request, Response $response, ?\Throwable $exception = null): void
    {
        // empty
    }

    public function getName(): string
    {
        return 'Honeypot';
    }

    public function reset(): void
    {
        self::$lastHoneyPotDetected = null;
    }
}
