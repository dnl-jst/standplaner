# Release Management

## Übersicht

Das Projekt verwendet automatisierte Docker-Builds über GitHub Actions. Images werden nur erstellt, wenn neue Version-Tags gepusht werden.

## Release erstellen

### Mit dem Release-Script (empfohlen)

```bash
./release.sh
```

Das Script führt automatisch folgende Schritte aus:
1. Prüft ob du auf dem `main` Branch bist
2. Prüft ob das Working Directory sauber ist
3. Pullt die neuesten Changes
4. Zeigt aktuelle Tags an
5. Fragt nach der neuen Version
6. Validiert das Versionsformat (vX.Y.Z)
7. Fragt nach Release Notes
8. Erstellt einen annotierten Git-Tag
9. Pusht den Tag zu GitHub

### Manuell

1. Stelle sicher, dass du auf dem `main` Branch bist und alle Changes committed sind:
```bash
git checkout main
git pull origin main
git status
```

2. Erstelle einen annotierten Tag:
```bash
git tag -a v1.0.0 -m "Release v1.0.0

- Neue Features
- Bugfixes
- Verbesserungen"
```

3. Push den Tag:
```bash
git push origin v1.0.0
```

## Versioning Schema

Das Projekt folgt **Semantic Versioning** (SemVer):
- Format: `vMAJOR.MINOR.PATCH` (z.B. `v1.2.3`)
- **MAJOR**: Breaking Changes (API-Änderungen, die Kompatibilität brechen)
- **MINOR**: Neue Features (rückwärtskompatibel)
- **PATCH**: Bugfixes (rückwärtskompatibel)

## Automatischer Build-Prozess

Wenn ein Tag mit dem Pattern `v*` gepusht wird:

1. GitHub Actions startet automatisch den Docker-Build
2. Das Image wird mit zwei Tags erstellt:
   - `ghcr.io/dnl-jst/standplaner:vX.Y.Z` (spezifische Version)
   - `ghcr.io/dnl-jst/standplaner:latest` (neueste Version)
3. Das Image wird zur GitHub Container Registry gepusht

## Deployment

### Production Update

1. Warte bis der Build erfolgreich abgeschlossen ist (check GitHub Actions)
2. Aktualisiere die `docker-compose.prod.yml`:
```yaml
services:
  app:
    image: ghcr.io/dnl-jst/standplaner:v1.0.0  # neue Version
```

3. Deploye die neue Version:
```bash
docker-compose -f docker-compose.prod.yml pull
docker-compose -f docker-compose.prod.yml up -d
```

### Rollback

Falls ein Rollback nötig ist, verwende einfach eine ältere Version:
```yaml
services:
  app:
    image: ghcr.io/dnl-jst/standplaner:v0.9.0  # vorherige Version
```

## Monitoring

- **Build Status**: https://github.com/dnl-jst/standplaner/actions
- **Container Registry**: https://github.com/dnl-jst/standplaner/pkgs/container/standplaner
- **Available Tags**: Siehe Container Registry oder `git tag --sort=-version:refname`

## Tipps

- Verwende aussagekräftige Release Notes
- Teste neue Features vor dem Release
- Halte die CI/CD Pipeline grün
- Dokumentiere Breaking Changes ausführlich
- Erstelle Releases regelmäßig für bessere Nachverfolgbarkeit
