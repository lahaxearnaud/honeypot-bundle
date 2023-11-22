<?php
declare(strict_types=1);

namespace Alahaxe\HoneypotBundle\Services\CounterMeasures;

use Alahaxe\HoneypotBundle\Enums\CloudflareBlockMode;
use Alahaxe\HoneypotBundle\Services\CounterMeasureInterface;
use Alahaxe\HoneypotBundle\Services\VisitorFingerPrintService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CloudflareCounterMeasure implements CounterMeasureInterface
{
    public const FIREWALL_API_ENDPOINT = 'https://api.cloudflare.com/client/v4/user/firewall/access_rules/rules';

    public function __construct(
        protected VisitorFingerPrintService $fingerPrintService,
        protected HttpClientInterface $client,
        protected readonly string $cloudflareEmail,
        protected readonly string $cloudflareToken,
        protected readonly CloudflareBlockMode $mode = CloudflareBlockMode::MODE_CHALLENGE,
        // https://developers.cloudflare.com/api/operations/ip-access-rules-for-an-account-create-an-ip-access-rule
        protected readonly string $cloudflareApiEndpoint = self::FIREWALL_API_ENDPOINT,
    ) {
    }

    public function react(Request $request, string $honeypotPattern): void
    {
        $this->client->request(
            Request::METHOD_POST,
            $this->cloudflareApiEndpoint,
            [
                'timeout' => 1,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-Auth-Email' => $this->cloudflareEmail,
                    'X-Auth-Key' => $this->cloudflareToken,
                ],
                'json' => [
                    'mode' => $this->mode->value,
                    'configuration' => [
                        'target' => 'ip',
                        'value' => $request->getClientIp(),
                    ],
                    'notes' => 'Blocked by PHP honeypot',
                ],
            ],
        );
    }
}
