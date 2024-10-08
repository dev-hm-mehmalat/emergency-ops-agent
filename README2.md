# Notfalldienst-Managementsystem

## Überblick

Dieses Projekt ist ein Notfalldienst-Managementsystem, das Daten aus einem CRM und SaaS-Diensten integriert, um Notfalldienstpläne zu verwalten und zu kommunizieren.

## Verzeichnisstruktur und Dateien

### 1. Commands

- **UpdateEmergencyPlan.php**
  - Pfad: `app/Console/Commands/UpdateEmergencyPlan.php`
  - Verantwortlich für das Abrufen von Notfalldienstinformationen aus dem CRM, das Überprüfen der Anwesenheit der Mitarbeiter über den SaaS-Dienst und das Senden von Benachrichtigungen an Rocket Chat. Dieser Command wird täglich um 12:00 Uhr ausgeführt.

### 2. Services

- **CRMService.php**
  - Pfad: `app/Services/CRMService.php`
  - Verantwortlich für die Interaktion mit dem CRM und das Verwalten von Notfalldienstplänen.

- **SaasService.php**
  - Pfad: `app/Services/SaasService.php`
  - Verantwortlich für die Interaktion mit dem SaaS-Dienst zur Überprüfung der Anwesenheit der Mitarbeiter und zur Aktualisierung der Notfalldienstpläne.

- **RocketChatService.php**
  - Pfad: `app/Services/RocketChatService.php`
  - Verantwortlich für das Senden von Nachrichten an Rocket Chat.

### 3. Console Kernel

- **Kernel.php**
  - Pfad: `app/Console/Kernel.php`
  - Registriert den `UpdateEmergencyPlan` Command und plant dessen tägliche Ausführung um 12:00 Uhr.

## Implementierte Funktionen

### Command: UpdateEmergencyPlan

- **handle()**
  - Holt Daten vom `CRMService`.
  - Holt Anwesenheitsdaten vom `SaasService`.
  - Filtert die Notfalldienst-Mitarbeiter basierend auf den Anwesenheitsdaten.
  - Sendet Benachrichtigungen an Rocket Chat über den `RocketChatService`.
  - Aktualisiert die Notfalldienstinformationen beim SaaS-Dienst.
  - Sendet die aktualisierten Notfalldienstinformationen an eine externe Webseite.

### CRMService

- **getData()**
  - Holt Daten vom CRM oder gibt Dummy-Daten zurück.

- **createEmergencyPlan($data)**
  - Erstellt einen neuen Notfalldienstplan (Dummy-Implementierung).

- **updateEmergencyPlan($id, $data)**
  - Aktualisiert einen bestehenden Notfalldienstplan (Dummy-Implementierung).

### SaasService

- **getStaffAttendance()**
  - Holt Anwesenheitsdaten vom SaaS-Dienst.

- **updateEmergencyPlan($data)**
  - Aktualisiert die Notfalldienstinformationen beim SaaS-Dienst.

- **notifyNewPlan($data), notifyUpdatedPlan($data), notifyDeletedPlan($id)**
  - Dummy-Implementierungen zum Senden von Benachrichtigungen über Änderungen im Notfalldienstplan.

### RocketChatService

- **sendMessage($message)**
  - Sendet eine Nachricht an einen Rocket Chat-Kanal.

## Bisher erstellte Dateien

1. `app/Console/Commands/UpdateEmergencyPlan.php`
2. `app/Services/CRMService.php`
3. `app/Services/SaasService.php`
4. `app/Services/RocketChatService.php`
5. `app/Console/Kernel.php`
6. `tests/Unit/CRMServiceTest.php`

## Nächste Schritte

1. **Integration echter API-Endpunkte**
   - Ersetzen der Dummy-URL in `RocketChatService` durch die tatsächliche URL des Rocket Chat Servers.
   - Verwenden echter API-Endpunkte für `SaasService` und `CRMService`.

2. **Erweitern der Testabdeckung**
   - Schreiben zusätzlicher Unit-Tests für alle Methoden und Szenarien.

3. **Dokumentation**
   - Fortlaufend aktualisieren und erweitern.

4. *****CI/CD einrichten**  ?
   - Einrichtung einer automatisierten Pipeline zur kontinuierlichen Integration und Bereitstellung.

## Konfigurationsdateien

### .env-Datei

Die `.env`-Datei enthält Konfigurationsparameter für die verschiedenen Dienste:

```plaintext
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:cMPaBoW/6FfPAZ4SNbNV2aeLDhWoLS7I6Ztw6PvFJDY=
APP_DEBUG=true
APP_TIMEZONE=UTC
APP_URL=http://localhost

CACHE_DRIVER=database
DB_CACHE_TABLE=cache
CACHE_PREFIX=myapp_cache_

CRM_API_KEY=your_crm_api_key
ROCKET_CHAT_API_KEY=your_auth_token
ROCKET_CHAT_USER_ID=your_user_id
ROCKET_CHAT_URL=https://your.rocket.chat.server/api/v1

SAAS_API_URL=https://desktop.saas.de/rest/api/time/overview/api
SAAS_API_UPDATE_URL=https://desktop.saas.de/rest/api/time/overview/update
SAAS_LOGIN_URL=https://desktop.saas.de/rest/api/v1/login
SAAS_USERNAME=myusername
SAAS_PASSWORD=mypassword
EXTERNAL_WEBSITE_API_URL=https://postman-echo.com/post
