<?php
declare(strict_types=1);

namespace Alahaxe\HoneypotBundle\Services\CounterMeasures;

use Alahaxe\HoneypotBundle\Services\CounterMeasureInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\Request;

class LoggerCounterMeasure implements CounterMeasureInterface, LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function react(Request $request, string $honeypotPattern): void
    {
        $path = $request->getPathInfo();

        $this->logger->warning(
            sprintf(
                'Path "%s" match honeypot pattern "%s" for ip "%s"',
                htmlentities($path),
                $honeypotPattern,
                implode(', ', $request->getClientIps())
            )
        );
    }
}
