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
    # | Required part
    # ------------------
    policies:
        - 'local_lock' # local lock, based on filesystem cache
        - 'cloudflare_lock' # lock on cloudflare

    # ------------------
    # | Optional part
    # ------------------
    ipWhitelist:
        - 127.0.0.1

    # ------------------
    # | Optional part, used only for local lock
    # ------------------
    lockTtl: 60 # (default: 60) duration in s of the local lock

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

## License

This bundle is under the MIT license. See the complete license [in the bundle](LICENSE).
