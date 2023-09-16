<?php
declare(strict_types=1);

namespace Alahaxe\HoneypotBundle\Services;

use Symfony\Component\HttpFoundation\Request;

class VisitorFingerPrintService
{
    public function getIpFingerPrint(string $ip): string
    {
        return 'alahaxe_honeypotBundle.'.hash('sha1', $ip);
    }

    public function getFingerPrint(Request $request): string
    {
        return $this->getIpFingerPrint((string) $request->getClientIp());
    }
}
