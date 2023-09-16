<?php
declare(strict_types=1);

namespace Alahaxe\HoneypotBundle\Enums;

enum Policy: string
{
    case POLICY_DEBUG = 'debug';
    case POLICY_LOG = 'log';
    case POLICY_LOCAL_LOCK = 'local_lock';
    case POLICY_CLOUDFLARE_LOCK = 'cloudflare_lock';
}
