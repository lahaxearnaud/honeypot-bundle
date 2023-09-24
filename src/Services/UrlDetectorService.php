<?php
declare(strict_types=1);

namespace Alahaxe\HoneypotBundle\Services;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\Request;

class UrlDetectorService implements LoggerAwareInterface
{

    use LoggerAwareTrait;

    /**
     * @param string[] $honeypotUrlPatterns
     */
    public function __construct(
        protected array $honeypotUrlPatterns = []
    ) {
    }

    public function getRequestHoneypot(Request $request): ?string
    {
        $path = $request->getPathInfo();
        foreach ($this->honeypotUrlPatterns as $honeypotUrlPattern) {
            if (fnmatch($honeypotUrlPattern, $path, FNM_CASEFOLD)) {
                return $honeypotUrlPattern;
            }
        }

        return null;
    }
}
