<?php
declare(strict_types=1);

namespace Alahaxe\HoneypotBundle\Services;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CounterMeasureManager implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @param iterable<CounterMeasureInterface> $counterMeasures
     * @param string[] $ipWhitelist
     * @param string[] $enabledCounterMeasures
     */
    public function __construct(
        protected iterable $counterMeasures,
        protected array $enabledCounterMeasures,
        protected array $ipWhitelist = []
    ) {
    }

    public function onRequest(Request $request): ?Response
    {
        if (in_array($request->getClientIp(), $this->ipWhitelist, true)) {
            return null;
        }

        foreach ($this->counterMeasures as $counterMeasure) {
            if (!$counterMeasure instanceof CounterMeasureRequestSubscriberInterface) {
                continue;
            }

            $class = get_class($counterMeasure);
            if (!in_array($class, $this->enabledCounterMeasures, true)) {
                continue;
            }

            $result = $counterMeasure->onRequest($request);

            if ($result !== null) {
                return $result;
            }
        }

        return null;
    }

    public function react(Request $request, string $honeypotPattern): void
    {
        if (in_array($request->getClientIp(), $this->ipWhitelist, true)) {
            return;
        }

        foreach ($this->counterMeasures as $counterMeasure) {
            $class = get_class($counterMeasure);
            if (!in_array($class, $this->enabledCounterMeasures, true)) {
                continue;
            }

            try {
                $counterMeasure->react($request, $honeypotPattern);
            } catch (\Throwable $t) {
                $this->logger->warning(
                    sprintf(
                        'Fail to react to honeypot trigger from %s: %s',
                        $class,
                        $t->getMessage()
                    )
                );
            }
        }
    }

    /**
     * @param array<string> $enabledCounterMeasures
     * @return void
     */
    public function setEnabledCounterMeasures(array $enabledCounterMeasures): void
    {
        $this->enabledCounterMeasures = $enabledCounterMeasures;
    }
}
