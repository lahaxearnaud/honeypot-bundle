<?php
declare(strict_types=1);

namespace Alahaxe\HoneypotBundle\Services\CounterMeasures;

use Alahaxe\HoneypotBundle\Services\CounterMeasureRequestSubscriberInterface;
use Alahaxe\HoneypotBundle\Services\LockedResponseGeneratorInterface;
use Alahaxe\HoneypotBundle\Services\VisitorFingerPrintService;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LocalLockCounterMeasure implements CounterMeasureRequestSubscriberInterface
{
    public function __construct(
        protected VisitorFingerPrintService $fingerPrintService,
        protected AdapterInterface $honeypotCache,
        protected LockedResponseGeneratorInterface $lockedResponseGenerator,
        protected int $lockTtl = 60,
    ) {
    }

    public function react(Request $request, string $honeypotPattern): void
    {
        $cacheItem = $this->honeypotCache->getItem($this->fingerPrintService->getFingerPrint($request));
        $cacheItem->expiresAfter($this->lockTtl);
        $cacheItem->set(1);
        $this->honeypotCache->save($cacheItem);
    }

    public function onRequest(Request $request): ?Response
    {
        if ($this->honeypotCache->hasItem($this->fingerPrintService->getFingerPrint($request))) {
            return $this->lockedResponseGenerator->generateResponse();
        }

        return null;
    }

    public function unlockIp(string $ip): void
    {
        $this->honeypotCache->deleteItem(
            $this->fingerPrintService->getIpFingerPrint($ip)
        );
    }
}
