# IPSymconHueSyncBox
[![IP-Symcon Module](https://img.shields.io/badge/IP--Symcon-Module-blue.svg)](https://www.symcon.de/)
[![Symcon Version](https://img.shields.io/badge/Symcon-%3E%205.1-green.svg)](https://www.symcon.de/service/dokumentation/installation/)
[![PHP Version](https://img.shields.io/badge/PHP-%3E%207.4-blue.svg)](https://www.php.net/)
[![License](https://img.shields.io/badge/License-GPLv3-blue.svg)](https://www.gnu.org/licenses/gpl-3.0.html)

Philips Hue Sync Box Modul für IP-Symcon.

Ermöglicht die Fernsteuerung und Statusabfrage einer Philips Hue Sync Box innerhalb von IP-Symcon.

---

## Dokumentation

**Inhaltsverzeichnis**

1. [Funktionsumfang](#1-funktionsumfang)  
2. [Voraussetzungen](#2-voraussetzungen)  
3. [Installation](#3-installation)  
4. [Funktionsreferenz](#4-funktionsreferenz)
5. [Konfiguration](#5-konfiguration)  
6. [Anhang](#6-anhang)  

## 1. Funktionsumfang

Mit der Philips Hue Sync Box ist es möglich, Philips Hue Lampen mit einem anliegenden Video- oder Audiosignal zu synchronisieren.
Das Modul ermöglicht die Fernsteuerung der Philips Hue Sync Box aus IP-Symcon sowie die Anzeige der aktuellen Werte der Hue Sync Box in IP-Symcon.

Setzen von:
- Synchronisierungsmodus
- Intensität
- Helligkeit
- HDMI-Input
- Ein- / Ausschalten
- Erweiterte Einstellungen

Auslesen der aktuellen Einstellungen der Hue Sync Box.

## 2. Voraussetzungen

- IPS > 5.1
- Philips Hue Sync Box

## 3. Installation

### a. Richten Sie die Sync Box mit der offiziellen Hue Sync iOS- / Android-App ein

Zunächst ist die Philips Hue Sync Box mit der offiziellen Hue Sync iOS- / Android-App einzurichten.
Nach der Ersteinrichtung ist darauf zu achten, dass ein Upgrade auf die aktuelle Firmware-Version durchgeführt wird.

### b. Laden des Moduls

Die Webkonsole von IP-Symcon über _http://{IP-Symcon IP}:3777/console/_ öffnen.

Anschließend oben rechts auf das Symbol für den Modulstore klicken.

![Store](img/store_icon.png?raw=true "open store")

Im Suchfeld nun

```
Philips Hue Sync Box
```

eingeben.

![Store](img/module_store_search.png?raw=true "module search")

Anschließend das Modul auswählen und auf _Installieren_ klicken.

![Store](img/install.png?raw=true "install")

### c. Einrichtung in IP-Symcon

Es wird automatisch eine Discovery-Instanz für die Philips Hue Sync Box erstellt. Befindet sich IP-Symcon im selben Netzwerk wie die Philips Hue Sync Box, wird diese gefunden und kann mit _Erstellen_ eine Instanz der Hue Sync Box in IP-Symcon erzeugen.

#### Registrierung der Hue Sync Box

Um IP-Symcon mit der Hue Sync Box zu verbinden, gehen Sie bitte wie folgt vor:

1. In der Instanz auf **_Registration_** klicken.
2. Es öffnet sich ein Popup-Fenster.
3. Innerhalb von 5 Sekunden den Button an der Hue Sync Box ca. 3 Sekunden gedrückt halten, bis die LED grün blinkt, anschließend loslassen.
4. Das Modul versucht automatisch, ein Access Token zu beziehen.

Falls die Registrierung nicht erfolgreich ist, kann der Vorgang im Popup über **_Retry_** erneut gestartet werden.

Nach erfolgreicher Registrierung wird das Access Token gespeichert und die erweiterten Einstellungen der Hue Sync Box stehen zur Verfügung.

In der Instanz kann ausgewählt werden, dass zusätzlich Skripte angelegt werden sollen.

![huescript](img/hue_sync_scripts.png?raw=true "Webfront")

Daraufhin werden Skripte für wichtige Funktionen automatisch erzeugt.

Unter _Eigenschaften_ können optional weitere Variablen ausgewählt werden, die im WebFront angezeigt werden sollen.

## 4. Funktionsreferenz

### WebFront-Ansicht

![webfront](img/hue_sync_webfront.png?raw=true "Webfront")

### Mögliche Ansicht in NEO

![viewneo](img/hue_sync_box_neo.png?raw=true "View NEO")

### Hue Sync Box steuern

Die Hue Sync Box kann ein- / ausgeschaltet werden, der HDMI-Input gewechselt werden sowie Modus und Intensität eingestellt werden.

## 5. Konfiguration

### Philips Hue Sync Box

| Eigenschaft    | Typ     | Standardwert | Funktion                         |
| :------------: | :-----: | :----------: | :------------------------------: |
| Host           | string  |              | IP-Adresse der Hue Sync Box     |
| Updateinterval | integer |              | Update-Intervall in Sekunden    |

## 6. Anhang

### a. Funktionen

#### Philips Hue Sync Box

**Basis-Geräteinformationen auslesen**
```php
HUESYNC_GetDeviceInfo(integer $InstanceID)
```
Parameter _$InstanceID_: ObjektID der Philips Hue Sync Box Instanz.

**IP-Symcon an der Philips Hue Sync Box registrieren**
```php
HUESYNC_Registration(integer $InstanceID)
```
Parameter _$InstanceID_: ObjektID der Philips Hue Sync Box Instanz.

#### Folgende Methoden funktionieren nur, nachdem IP-Symcon erfolgreich an der Hue Sync Box registriert wurde

**Aktuellen Status der Philips Hue Sync Box auslesen**
```php
HUESYNC_GetCurrentState(integer $InstanceID)
```
Parameter _$InstanceID_: ObjektID der Philips Hue Sync Box Instanz.

**Modus einstellen**
```php
HUESYNC_Mode(integer $InstanceID, string $mode)
```
Parameter _$InstanceID_: ObjektID der Philips Hue Sync Box Instanz.

Parameter _$mode_: passthrough | powersave | video | music | game

**Helligkeit einstellen**
```php
HUESYNC_Brightness(integer $InstanceID, integer $brightness)
```
Parameter _$InstanceID_: ObjektID der Philips Hue Sync Box Instanz.

Parameter _$brightness_: 0 - 200

**Intensität einstellen**
```php
HUESYNC_Intensity(integer $InstanceID, string $mode, string $intensity)
```
Parameter _$InstanceID_: ObjektID der Philips Hue Sync Box Instanz.

Parameter _$mode_: video | music | game

Parameter _$intensity_: subtle | moderate | high | intense

**ARC Bypass**
```php
HUESYNC_ARC_Bypass(integer $InstanceID)
```
Parameter _$InstanceID_: ObjektID der Philips Hue Sync Box Instanz.

**Auto Switch Inputs**
```php
HUESYNC_AutoSwitchInputs(integer $InstanceID, integer $input, boolean $state)
```
Parameter _$InstanceID_: ObjektID der Philips Hue Sync Box Instanz.

Parameter _$input_: 1 - 4
Parameter _$state_: true | false

**Auto Sync Inputs**
```php
HUESYNC_AutoSync(integer $InstanceID, integer $input, boolean $state)
```
Parameter _$InstanceID_: ObjektID der Philips Hue Sync Box Instanz.

Parameter _$input_: 1 - 4
Parameter _$state_: true | false

**CEC Power State Detection**
```php
HUESYNC_CEC_PowerStateDetection(integer $InstanceID, boolean $state)
```
Parameter _$InstanceID_: ObjektID der Philips Hue Sync Box Instanz.

Parameter _$state_: true | false

**Kontrollierte Entertainment Area definieren**
```php
HUESYNC_DefineControlledEntertainmentArea(integer $InstanceID, string $area_id)
```
Parameter _$InstanceID_: ObjektID der Philips Hue Sync Box Instanz.

Parameter _$area_id_: Entertainment Area ID

**Input-Namen festlegen**
```php
HUESYNC_DefineInputNames(integer $InstanceID, string $name1, string $name2, string $name3, string $name4)
```
Parameter _$InstanceID_: ObjektID der Philips Hue Sync Box Instanz.

Parameter _$name1_: Input-Name für HDMI 1
Parameter _$name2_: Input-Name für HDMI 2
Parameter _$name3_: Input-Name für HDMI 3
Parameter _$name4_: Input-Name für HDMI 4

**HDMI Inactivity Power State**
```php
HUESYNC_HDMI_InactivityPowerState(integer $InstanceID, boolean $state)
```
Parameter _$InstanceID_: ObjektID der Philips Hue Sync Box Instanz.

Parameter _$state_: true | false

**HDMI Input Detected**
```php
HUESYNC_HDMI_InputDetected(integer $InstanceID, boolean $state)
```
Parameter _$InstanceID_: ObjektID der Philips Hue Sync Box Instanz.

Parameter _$state_: true | false

**Hue Sync Box einschalten**
```php
HUESYNC_PowerOn(integer $InstanceID)
```
Parameter _$InstanceID_: ObjektID der Philips Hue Sync Box Instanz.

**Hue Sync Box ausschalten**
```php
HUESYNC_PowerOff(integer $InstanceID)
```
Parameter _$InstanceID_: ObjektID der Philips Hue Sync Box Instanz.

**Hue Sync Box umschalten**
```php
HUESYNC_PowerToggle(integer $InstanceID)
```
Parameter _$InstanceID_: ObjektID der Philips Hue Sync Box Instanz.

**Hue Sync Box neu starten**
```php
HUESYNC_RestartSyncBox(integer $InstanceID)
```
Parameter _$InstanceID_: ObjektID der Philips Hue Sync Box Instanz.

**Hintergrundbeleuchtung umschalten**
```php
HUESYNC_BackgroundLighting(integer $InstanceID, string $mode, boolean $state)
```
Parameter _$InstanceID_: ObjektID der Philips Hue Sync Box Instanz.

Parameter _$mode_: video | game
Parameter _$state_: true | false

**USB Power State Detection**
```php
HUESYNC_USB_PowerStateDetection(integer $InstanceID, boolean $state)
```
Parameter _$InstanceID_: ObjektID der Philips Hue Sync Box Instanz.

Parameter _$state_: true | false

**LED Mode**
```php
HUESYNC_LEDMode(integer $InstanceID, int $mode)
```
Parameter _$InstanceID_: ObjektID der Philips Hue Sync Box Instanz.

Parameter _$mode_: 0 = off | 1 = regular | 2 = dimmed

**Vorheriger Sync-Modus**
```php
HUESYNC_PreviousSyncMode(integer $InstanceID)
```
Parameter _$InstanceID_: ObjektID der Philips Hue Sync Box Instanz.

**Nächster Sync-Modus**
```php
HUESYNC_NextSyncMode(integer $InstanceID)
```
Parameter _$InstanceID_: ObjektID der Philips Hue Sync Box Instanz.

**Vorherige HDMI-Quelle**
```php
HUESYNC_PreviousHDMISource(integer $InstanceID)
```
Parameter _$InstanceID_: ObjektID der Philips Hue Sync Box Instanz.

**Nächste HDMI-Quelle**
```php
HUESYNC_NextHDMISource(integer $InstanceID)
```
Parameter _$InstanceID_: ObjektID der Philips Hue Sync Box Instanz.

**Vorherige Intensität**
```php
HUESYNC_PreviousIntensity(integer $InstanceID)
```
Parameter _$InstanceID_: ObjektID der Philips Hue Sync Box Instanz.

**Nächste Intensität**
```php
HUESYNC_NextIntensity(integer $InstanceID)
```
Parameter _$InstanceID_: ObjektID der Philips Hue Sync Box Instanz.

**Palette**
```php
HUESYNC_Palette(integer $InstanceID, string $palette)
```
Parameter _$InstanceID_: ObjektID der Philips Hue Sync Box Instanz.

Parameter _$palette_: happyEnergetic | happyCalm | melancholicCalm | melancholicEnergetic | neutral

### b. GUIDs und Datenaustausch

#### Philips Hue Sync Box

GUID: `{716FA5CE-2292-8EA5-78F9-8B245EFAF0A7}`