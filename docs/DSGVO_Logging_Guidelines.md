# DSGVO-konforme Logging-Richtlinien

## Was NICHT geloggt werden darf (personenbezogene Daten):
- IP-Adressen
- E-Mail-Adressen (nur gehashte/anonymisierte Versionen)
- Vollständige Namen (nur Initialen oder anonymisierte IDs)
- Telefonnummers
- Adressen
- Session-IDs oder ähnliche Identifikatoren

## Was geloggt werden kann:
- Anzahl der Aktionen/Einträge
- Timestamps
- Funktionale Ereignisse (Login-Versuche, Fehler)
- Technische Metriken (Performance, Errors)
- Anonymisierte Statistiken

## Beispiele für DSGVO-konforme Logs:
```
✅ "User registration completed, 5 stands registered"
✅ "Login attempt failed: invalid credentials"
✅ "Database migration completed in 2.3 seconds"

❌ "User john.doe@example.com registered from IP 192.168.1.1"
❌ "Login failed for user ID 123 from session abc123"
```

## Empfohlene Praxis:
- Namen nur für funktionale Zwecke loggen (z.B. Erfolgsbestätigungen)
- Bei Fehlern: anonyme Identifier verwenden
- Logs regelmäßig rotieren und löschen
- In Production: nur INFO-Level und höher loggen
