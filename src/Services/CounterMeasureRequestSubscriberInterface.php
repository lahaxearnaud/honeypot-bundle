<?php
declare(strict_types=1);

namespace Alahaxe\HoneypotBundle\Services;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface CounterMeasureRequestSubscriberInterface extends CounterMeasureInterface
{
    public function onRequest(Request $request): ?Response;
}
