<?php

namespace Alahaxe\HoneypotBundle\Services\LockedResponseGenerator;

use Alahaxe\HoneypotBundle\Services\LockedResponseGeneratorInterface;
use Symfony\Component\HttpFoundation\Response;

class SimpleResponseGenerator implements LockedResponseGeneratorInterface
{
    public function generateResponse(): Response
    {
        return new Response(
            '',
            Response::HTTP_LOCKED
        );
    }
}
