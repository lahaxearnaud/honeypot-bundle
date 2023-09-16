<?php

declare(strict_types=1);

namespace Alahaxe\HoneypotBundle\Enums;

enum CloudflareBlockMode: string
{
    case MODE_BLOCK = "block";
    case MODE_CHALLENGE = "challenge";
    case MODE_JS_CHALLENGE = "js_challenge";
    case MODE_MANAGED_CHALLENGE = "managed_challenge";
}
