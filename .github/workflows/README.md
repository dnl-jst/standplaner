# CI/CD Pipeline

Diese GitHub Actions Pipeline führt automatisierte Tests und Qualitätsprüfungen für das Standplaner-Projekt durch.

## Pipeline-Jobs

### 1. Tests (`tests`)
- **PHP 8.2** mit PostgreSQL 16
- **Composer-Dependencies** installieren
- **Datenbank** erstellen und Migrationen ausführen
- **PHPUnit-Tests** mit Code Coverage
- **Coverage-Reports** an Codecov senden

### 2. Security Check (`security-check`)
- **Sicherheitsprüfung** der Dependencies
- **Vulnerability-Scan** mit Symfony Security Checker

### 3. Code Quality (`lint`)
- **Twig-Templates** validieren
- **YAML-Konfiguration** prüfen
- **Symfony Container** validieren

### 4. Static Analysis (`static-analysis`)
- **PHPStan** Code-Analyse (falls konfiguriert)
- **Code-Quality** Checks

## Trigger

Die Pipeline wird ausgeführt bei:
- **Push** auf `main` oder `develop` Branch
- **Pull Requests** auf `main` oder `develop` Branch

## Konfiguration

### Umgebungsvariablen
- `DATABASE_URL`: PostgreSQL Test-Datenbank
- `APP_ENV`: Test-Umgebung

### Services
- **PostgreSQL 16** mit Health Checks
- **PHP 8.2** mit allen benötigten Extensions

### Cache
- **Composer-Cache** für schnellere Builds
- **Dependencies** werden zwischen Jobs gecacht

## Ergebnisse

Nach jedem Lauf erhalten Sie:
- ✅ **Test-Ergebnisse** von PHPUnit
- 📊 **Code Coverage** Report
- 🔒 **Security-Status** der Dependencies
- 🔍 **Code-Quality** Checks
- 📝 **Lint-Ergebnisse** für Templates und Konfiguration

## Status Badges

Sie können Status-Badges in Ihr README einfügen:

```markdown
![CI](https://github.com/dnl-jst/standplaner/workflows/CI%20Pipeline/badge.svg)
```
