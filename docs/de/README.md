# IPSymconHueSyncBox
[![Version](https://img.shields.io/badge/Symcon-PHPModul-red.svg)](https://www.symcon.de/service/dokumentation/entwicklerbereich/sdk-tools/sdk-php/)
[![Version](https://img.shields.io/badge/Symcon%20Version-%3E%205.1-green.svg)](https://www.symcon.de/service/dokumentation/installation/)

Modul für IP-Symcon ab Version 5.1. Ermöglicht das Senden von Befehlen an eine Philips Hue Sync Box und das Anzeigen des Status in IP-Symcon.

## Dokumentation

**Inhaltsverzeichnis**

1. [Funktionsumfang](#1-funktionsumfang)  
2. [Voraussetzungen](#2-voraussetzungen)  
3. [Installation](#3-installation)  
4. [Funktionsreferenz](#4-funktionsreferenz)
5. [Konfiguration](#5-konfiguartion)  
6. [Anhang](#6-anhang)  

## 1. Funktionsumfang

Mit der Philips Hue Sync Box ist es möglich Philips Hue Lampen mit einem anliegenden Video oder Audio Signal zu sychronisieren.
Das Modul erlaubt die fernsteuerung der Philips Hue Sync Box aus IP-Symcon und das anzeigen der aktuellen Werte der Philips Hue Sync Box in IP-Symcon.

Setzen von:
- Synchronisierungsmodus
- Intensität
- Helligkeit
- HDMI Input
- ein- / ausschalten
- Erweiterte Einstellungen

Auslesen der aktuellen Einstellungen der Hue Sync Box

## 2. Voraussetzungen

 - IPS > 5.1
 - Philips Hue Sync Box

## 3. Installation

### a. Richten Sie die Sync Box mit der offiziellen Hue Sync iOS / Android App ein

Zunächst ist die Philps Hue Sync Box mit der offiziellen Hue Sync iOS / Android App einzurichten.
Nach der Ersteinrichtung ist darauf zu achten dass ein Upgrade auf die aktuelle Firmware Version durchgeführt wird.

### b. Laden des Moduls

Die Webconsole von IP-Symcon mit _http://{IP-Symcon IP}:3777/console/_ öffnen. 


Anschließend oben rechts auf das Symbol für den Modulstore klicken

![Store](img/store_icon.png?raw=true "open store")

Im Suchfeld nun

```
Philips Hue Sync Box
```  

eingeben

![Store](img/module_store_search.png?raw=true "module search")

und schließend das Modul auswählen und auf _Installieren_

![Store](img/install.png?raw=true "install")

drücken.

### c. Einrichtung in IP-Symcon

Es wird automatisch eine Discovery Instanz für die Philips Hue Sync Box erstellt. Sollte sich IP-Symcon im gleichem Netzwerk befinden wie die Philips Hue Sync Box wird diese gefunden und kann dann mit _Erstellen_ eine
Instanz der Philips Hue Sync Box in IP-Symcon erzeugt werden. 

An der Hue Sync Box den Button gedrückt halten bis die LED grün leuchtet und zeitgleich in der Instanz unten auf _Registrieren_ drücken. Es sollte ein Token bezogen werden.

In der Instanz kann man auswählen das zusätzlich Skripte angelegt werden sollen

![huescript](img/hue_sync_scripts.png?raw=true "Webfront")

daraufhin werden zusätzlich Skripte für wichtige Funktionen automatisch erzeugt.

Unter _Eigenschaften_ können optional noch weitere Variablen ausgewählt werden, die im Webfront angezeigt werden sollen.

## 4. Funktionsreferenz

### Webfront Ansicht

![webfront](img/hue_sync_webfront.png?raw=true "Webfront")

### mögliche Ansicht in NEO

![viewneo](img/hue_sync_box_neo.png?raw=true "View NEO")

### Hue Sync Box schalten

Die Hue Sync Box kann ein- / ausgeschaltet werden, der Input gewechselt werden und der Modus und Instensität eingestellt werden.


## 5. Konfiguration:

### Philips Hue Sync Box:

| Eigenschaft   | Typ     | Standardwert | Funktion                           |
| :-----------: | :-----: | :----------: | :--------------------------------: |
| Host          | string  |              | IP Adresse der Steckdosenleiste    |
| Updateinterval| integer |              | Updateinterval in Sekunden         |


## 6. Anhang

###  a. Funktionen:

#### Philips Hue Sync Box:

**Basis Geräte Informationen auslesen**
```php
HUESYNC_GetDeviceInfo(integer $InstanceID)
``` 
Parameter _$InstanceID_: ObjektID der Philips Hue Sync Box Instanz.

**IP-Symcon an der Philips Hue Sync Box registrieren**
```php
HUESYNC_Registration(integer $InstanceID)
``` 
Parameter _$InstanceID_: ObjektID der Philips Hue Sync Box Instanz.


#### Folgende Methoden funktionieren nur nachdem IP-Symcon erfolgreich an der Hue Sync Box registriert wurde

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

**Auto switch Inputs**
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

**Definiere kontrollierte Entertainment Area**
```php
HUESYNC_DefineControlledEntertainmentArea(integer $InstanceID, string $area_id)
``` 
Parameter _$InstanceID_: ObjektID der Philips Hue Sync Box Instanz.

Parameter _$area_id_:  entertainment area id

**Input Namen festlegen**
```php
HUESYNC_DefineInputNames(integer $InstanceID, string $name1, string $name2, string $name3, string $name4)
``` 
Parameter _$InstanceID_: ObjektID der Philips Hue Sync Box Instanz.

Parameter _$mode_: video | music | game

Parameter _$name1_: input name input 1

Parameter _$name2_: input name input 2

Parameter _$name3_: input name input 3

Parameter _$name4_: input name input 4

**HDMI inactivity power state**
```php
HUESYNC_HDMI_InactivityPowerState(integer $InstanceID, boolean $state)
``` 
Parameter _$InstanceID_: ObjektID der Philips Hue Sync Box Instanz.

Parameter _$state_: true | false

**HDMI input detected**
```php
HUESYNC_HDMI_InputDetected(integer $InstanceID, boolean $state)
``` 
Parameter _$InstanceID_: ObjektID der Philips Hue Sync Box Instanz.

Parameter _$state_: true | false

**Power on Philips Hue Sync Box**
```php
HUESYNC_PowerOn(integer $InstanceID)
``` 
Parameter _$InstanceID_: ObjektID der Philips Hue Sync Box Instanz.

**Power off Philips Hue Sync Box**
```php
HUESYNC_PowerOff(integer $InstanceID)
``` 
Parameter _$InstanceID_: ObjektID der Philips Hue Sync Box Instanz.

**Power toggle Philips Hue Sync Box**
```php
HUESYNC_PowerToggle(integer $InstanceID)
``` 
Parameter _$InstanceID_: ObjektID der Philips Hue Sync Box Instanz.

**Restart Philips Hue Sync Box**
```php
HUESYNC_RestartSyncBox(integer $InstanceID)
``` 
Parameter _$InstanceID_: ObjektID der Philips Hue Sync Box Instanz.

**Toogle Hintergrund Beleuchtung**
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

**Previous SyncMode**
```php
HUESYNC_PreviousSyncMode(integer $InstanceID)
``` 
Parameter _$InstanceID_: ObjektID der Philips Hue Sync Box Instanz.

**Next SyncMode**
```php
HUESYNC_NextSyncMode(integer $InstanceID)
``` 
Parameter _$InstanceID_: ObjektID der Philips Hue Sync Box Instanz.

**Previous HDMI Source**
```php
HUESYNC_PreviousHDMISource(integer $InstanceID)
``` 
Parameter _$InstanceID_: ObjektID der Philips Hue Sync Box Instanz.

**Next SyncMode**
```php
HUESYNC_NextHDMISource(integer $InstanceID)
``` 
Parameter _$InstanceID_: ObjektID der Philips Hue Sync Box Instanz.

**Previous Intensity**
```php
HUESYNC_PreviousIntensity(integer $InstanceID)
``` 
Parameter _$InstanceID_: ObjektID der Philips Hue Sync Box Instanz.

**Next Intensity**
```php
HUESYNC_NextIntensity(integer $InstanceID)
``` 
Parameter _$InstanceID_: ObjektID der Philips Hue Sync Box Instanz.

**Palette**
```php
HUESYNC_Palette(integer $InstanceID, string $palette)
``` 
Parameter _$InstanceID_: ObjektID der Philips Hue Sync Box Instanz.

Parameter _$palette_: happyEnergetic, happyCalm, melancholicCalm, melancholic Energetic, neutral

	 
###  b. GUIDs und Datenaustausch:

#### Philips Hue Sync Box:

GUID: `{716FA5CE-2292-8EA5-78F9-8B245EFAF0A7}` 