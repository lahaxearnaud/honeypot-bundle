<?php
declare(strict_types=1);

namespace Alahaxe\HoneypotBundle\Services;

use Symfony\Component\HttpFoundation\Response;

interface LockedResponseGeneratorInterface
{
    public function generateResponse(): Response;
}
