# Production Deployment Guide

Dieses Dokument beschreibt, wie die Standplaner-Anwendung in Production deployed wird.

## 🐳 Docker-basiertes Deployment

### Voraussetzungen

- Docker und Docker Compose auf dem Server installiert
- Domain mit DNS-Konfiguration
- SSL-Zertifikate (automatisch via Let's Encrypt)

### Schnellstart

1. **Repository klonen:**
   ```bash
   git clone https://github.com/dnl-jst/standplaner.git
   cd standplaner
   ```

2. **Environment konfigurieren:**
   ```bash
   cp .env.prod.example .env.prod
   nano .env.prod  # Konfiguration anpassen
   ```

3. **Deployment ausführen:**
   ```bash
   ./deploy.sh
   ```

### Environment-Variablen

Kopiere `.env.prod.example` zu `.env.prod` und konfiguriere:

- `DOMAIN`: Deine Domain (z.B. `standplaner.example.com`)
- `APP_SECRET`: Sicherer, zufälliger String für Symfony
- `POSTGRES_*`: Datenbank-Konfiguration
- `ACME_EMAIL`: E-Mail für Let's Encrypt Zertifikate
- `TRAEFIK_AUTH`: Basic Auth für Traefik Dashboard

### Services

Das Setup startet folgende Services:

- **app**: Symfony-Anwendung mit Nginx + PHP-FPM
- **db**: PostgreSQL 16 Datenbank
- **redis**: Redis für Caching/Sessions
- **traefik**: Reverse Proxy mit automatischen SSL-Zertifikaten

### URLs

- Anwendung: `https://your-domain.com`
- Traefik Dashboard: `https://traefik.your-domain.com`

### Backup

Backup der Datenbank erstellen:
```bash
docker compose -f docker-compose.prod.yml --profile backup run --rm backup
```

### Monitoring

Services überwachen:
```bash
docker compose -f docker-compose.prod.yml ps
docker compose -f docker-compose.prod.yml logs -f app
```

### Updates

1. Neues Image wird automatisch bei Git-Push gebaut
2. Deployment mit: `./deploy.sh`

## 🔧 Manuelle Konfiguration

### Traefik Auth generieren
```bash
htpasswd -nb admin your-password
```

### SSL-Zertifikate erneuern
Traefik erneuert automatisch via Let's Encrypt.

### Datenbank-Migration
```bash
docker compose -f docker-compose.prod.yml exec app php bin/console doctrine:migrations:migrate
```

## 🚨 Troubleshooting

### Container-Logs ansehen
```bash
docker compose -f docker-compose.prod.yml logs app
```

### In Container einsteigen
```bash
docker compose -f docker-compose.prod.yml exec app sh
```

### Services neustarten
```bash
docker compose -f docker-compose.prod.yml restart app
```

## 📊 Performance

Das Setup ist optimiert für Production:
- OPcache aktiviert
- Nginx mit Gzip-Kompression
- Static Assets mit Cache-Headers
- Multi-stage Docker Build
- Health Checks

## 🔒 Sicherheit

- SSL/TLS via Let's Encrypt
- Security Headers in Nginx
- Container laufen als Non-Root User
- Sensible Verzeichnisse blockiert
- PHP-Konfiguration gehärtet
