# Honeypot Bundle

## Purpose of this bundle

Detect and react to directory scanning of your website.

## Features

- Detect scanning
- Log information about the attacker
- Block locally attacker ip
- Block / force a challenge on cloudflare firewall

## Install

```bash
composer require alahaxe/honeypot-bundle
```

## Configuration

```yaml
honeypot:
    # ------------------
    # | Required part, Policies are just some preset of counterMeasures
    # | You can activate several policies
    # ------------------
    policies: # values: debug, log, local_lock, cloudflare_lock
        - 'local_lock' # local lock, based on filesystem cache
        - 'cloudflare_lock' # lock on cloudflare's firewall using API
        - 'debug' # only used in unit tests
        - 'log' # enabled by default, just add a log when an honeypot is called

    # ------------------
    # | Optional part, if you create you own counter measure
    # ------------------
    counterMeasures: # All class listed here must also be symfony service with tag: alahaxe.honeypot.countermeasure
        - Alahaxe\HoneypotBundle\Services\CounterMeasures\DebugCounterMeasure

    # ------------------
    # | Optional part, but you should add your IP here
    # ------------------
    ipWhitelist:
        - 127.0.0.1

    # ------------------
    # | Optional part, used only for local lock
    # ------------------
    localLock:
        # (default: 60) duration in s of the local lock, this config is not used for cloudflare lock
        lockTtl: 60
        # Service that implements LockedResponseGeneratorInterface
        # default is Alahaxe\HoneypotBundle\Services\LockedResponseGenerator\SimpleResponseGenerator
        # but if you want a foncy page you can use TwigResponseGenerator
        renderService: 'Alahaxe\HoneypotBundle\Services\LockedResponseGenerator\TwigResponseGenerator'
        # If you use TwigResponseGenerator you may change the default template
        twigTemplate: 'YouTwigTemplateFile.html.twig'

    # ------------------
    # | Optional part, used only if you enable cloudflare policy
    # ------------------
    cloudflare:
        email: 'your cloudflare email' # you should use env var for this one
        token: 'your cloudflare api token' # you should use env var for this one
        mode: 'challenge' # One of : block, challenge, js_challenge, managed_challenge see

    # ------------------
    # | Optional part, default file contains commons scanned url
    # ------------------
    patternsFile: 'src/Resources/patterns.txt'
```

If you use twig render you need you register the namespace in you twig configuration:

```yaml
twig:
    paths:
        '%kernel.project_dir%/templates': ''
        '%kernel.project_dir%/vendor/alahaxe/honeypot-bundle/Resources/views': 'HoneypotBundle'
```

## Add you own counter measure

### Implements your own service

A counter measure is a simple symfony service that implements `Alahaxe\HoneypotBundle\Services\CounterMeasureInterface`.

For example:

```php
<?php
declare(strict_types=1);

namespace Alahaxe\HoneypotBundle\Services\CounterMeasures;

use Alahaxe\HoneypotBundle\Services\CounterMeasureInterface;
use Symfony\Component\HttpFoundation\Request;

class LoggerCounterMeasure implements CounterMeasureInterface
{
    public function react(Request $request, string $honeypotPattern): void
    {
        // do something
    }
}

```

### Register you service

All counter measure must have tag: `alahaxe.honeypot.countermeasure`

In you `service.yaml`:

```yaml
services:
    App\Services\Honeypot\CounterMeasures\DummyCounterMeasure:
        # ... your service config
        tags: ['alahaxe.honeypot.countermeasure']
```

Then you can activate your counter measure in the bundle configuration:

```yaml
honeypot:
    counterMeasures:
        - App\Services\Honeypo\CounterMeasures\DummyCounterMeasure
```

## License

This bundle is under the MIT license. See the complete license [in the bundle](LICENSE).
