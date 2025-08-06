# Trusted Proxies Konfiguration

## Problem
Wenn die Anwendung hinter einem Reverse Proxy (wie Nginx, Traefik, oder Load Balancer) läuft, 
kann Symfony die ursprünglichen Client-Informationen nicht korrekt verarbeiten. Dies führt zu:

- CSRF-Validierungsfehlern
- Falschen IP-Adressen in Logs
- Problemen mit HTTPS-Detection
- Fehlerhaften URLs in generierten Links

## Lösung
Die Trusted Proxies Konfiguration teilt Symfony mit, welchen Proxies es vertrauen soll:

```yaml
# config/packages/prod/framework.yaml
framework:
    trusted_proxies: '%env(TRUSTED_PROXIES)%'
    trusted_headers: 
        - 'x-forwarded-for'      # Ursprüngliche Client-IP
        - 'x-forwarded-host'     # Ursprünglicher Host-Header
        - 'x-forwarded-proto'    # Ursprüngliches Protokoll (http/https)
        - 'x-forwarded-port'     # Ursprünglicher Port
        - 'x-forwarded-prefix'   # URL-Prefix
```

## Umgebungsvariable
```bash
# Private Netzwerk-Ranges (RFC 1918)
TRUSTED_PROXIES=127.0.0.0/8,10.0.0.0/8,172.16.0.0/12,192.168.0.0/16
```

## Docker-spezifische Hinweise
- In Docker-Umgebungen sind Container typischerweise in privaten Netzwerken
- Die Standard-Ranges decken alle Docker-Netzwerk-Modi ab
- Bei Custom-Netzwerken ggf. spezielle Ranges hinzufügen

## Sicherheitshinweise
- Nur vertrauenswürdigen Proxies vertrauen
- Bei öffentlichen Load Balancern spezifische IPs verwenden
- Regelmäßig überprüfen ob alle Ranges noch aktuell sind
