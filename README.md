# Standplaner

Ein Symfony-basiertes System zur Verwaltung von Wahlkampfständen und Teilnehmern.

## Features

- **Wahlkampfstände verwalten**: Termine, Orte und Stadtteile organisieren
- **Teilnehmer-Management**: Helfer und ihre Teilnahmestatus verfolgen
- **Responsive UI**: Bootstrap 5 für moderne Benutzeroberfläche
- **Robuste Tests**: Umfassende PHPUnit-Test-Suite
- **Docker-Support**: PostgreSQL-Datenbank via Docker

## Technologie-Stack

- **Backend**: Symfony 7.3, Doctrine ORM, PHP 8.2+
- **Datenbank**: PostgreSQL 16
- **Frontend**: Bootstrap 5.3.7, Twig Templates
- **Testing**: PHPUnit 12.3.0
- **Container**: Docker & Docker Compose

## Installation

1. Repository klonen:
```bash
git clone https://github.com/dnl-jst/standplaner.git
cd standplaner
```

2. Dependencies installieren:
```bash
composer install
```

3. Docker-Container starten:
```bash
docker-compose up -d
```

4. Datenbank-Migration ausführen:
```bash
php bin/console doctrine:migrations:migrate
```

5. Entwicklungsserver starten:
```bash
symfony serve
```

## Tests ausführen

```bash
php bin/phpunit
```

## Lizenz

Dieses Projekt steht unter der MIT-Lizenz. Siehe [LICENSE](LICENSE) Datei für Details.

## Beiträge

Beiträge sind willkommen! Bitte erstelle einen Pull Request oder öffne ein Issue.
