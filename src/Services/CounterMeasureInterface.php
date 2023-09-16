<?php
declare(strict_types=1);

namespace Alahaxe\HoneypotBundle\Services;

use Symfony\Component\HttpFoundation\Request;

interface CounterMeasureInterface
{
    public function react(Request $request, string $honeypotPattern): void;
}
