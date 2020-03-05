# IPSymconHueSyncBox
[![Version](https://img.shields.io/badge/Symcon-PHPModule-red.svg)](https://www.symcon.de/service/dokumentation/entwicklerbereich/sdk-tools/sdk-php/)
[![Version](https://img.shields.io/badge/Symcon%20Version-%3E%205.1-green.svg)](https://www.symcon.de/en/service/documentation/installation/)

Module for IP Symcon Version 5.1 or higher. Allows you to send commands to the Philips Hue Sync Box und receive the current state from the device in IP-Symcon.

## Documentation

**Table of Contents**

1. [Features](#1-features)
2. [Requirements](#2-requirements)
3. [Installation](#3-installation)
4. [Function reference](#4-functionreference)
5. [Configuration](#5-configuration)
6. [Annex](#6-annex)

## 1. Features

With the Philips Hue Sync Box it is possible to synchronize Philips Hue lamps with an attached video or audio signal.
The module allows remote control of the Philips Hue Sync Box from IP-Symcon and the display of the current values of the Philips Hue Sync Box in IP-Symcon.

Setting of:
- synchronization mode
- intensity
- brightness
- HDMI input
- switch on / off
- Advanced settings

## 2. Requirements

 - IPS > 5.1
 - Philips Hue Sync Box

## 3. Installation

### a.  Set up Sync Box with the official Hue Sync iOS/Android app

First, the Philps Hue Sync Box must be set up with the official Hue Sync iOS / Android app.
After the initial setup, make sure that you upgrade to the current firmware version.

### b. Loading the module

Open the IP Console's web console with _http://{IP-Symcon IP}:3777/console/_.

Then click on the module store icon in the upper right corner.

![Store](img/store_icon.png?raw=true "open store")

In the search field type

```
Philips Hue Sync Box
```  


![Store](img/module_store_search_en.png?raw=true "module search")

Then select the module and click _Install_

![Store](img/install_en.png?raw=true "install")

### c.  Setup in IP-Symcon

A discovery instance for the Philips Hue Sync Box is created automatically. If IP-Symcon is in the same network as the Philips Hue Sync Box, it will be found and can then create one with _Create_
An instance of the Philips Hue Sync Box will be created in IP-Symcon.

Press and hold the button on the Hue Sync Box until the LED lights up green and at the same time press _Registrate_ in the instance below. A token should be obtained.

In the instance you can choose that additional scripts should be created

![huescript](img/hue_sync_scripts.png?raw=true "Webfront")

then scripts for important functions are automatically generated.

Additional variables can optionally be selected under _Properties_, which should be displayed on the web front.

## 4. Function reference

### Webfront View

![webfront](img/hue_sync_webfront.png?raw=true "Webfront")

### possible view in NEO

![viewneo](img/hue_sync_box_neo.png?raw=true "View NEO")

### Philips Hue Sync Box:

The Hue Sync Box can be switched on / off, the input can be changed and the mode and intensity can be set.

## 5. Configuration:

### Philips Hue Sync Box:

| Property      | Type    | Value        | Description                        |
| :-----------: | :-----: | :----------: | :--------------------------------: |
| Host          | string  |              | IP Adress                          |
| Updateinterval| integer |              | Updateinterval in Seconds          |


## 6. Annex

###  a. Functions:

#### Philips Hue Sync Box:

**Get basic device information**
```php
HUESYNC_GetDeviceInfo(integer $InstanceID)
``` 
Parameter _$InstanceID_ ObjectID of the Philips Hue Sync Box 

**Registration of IP-Symcon on the Philips Hue Sync Box**
```php
HUESYNC_Registration(integer $InstanceID)
``` 
Parameter _$InstanceID_ ObjectID of the Philips Hue Sync Box 

#### The following methods only work after IP-Symcon has been successfully registered on the Hue Sync Box

**Get current state from the Philips Hue Sync Box**
```php
HUESYNC_GetCurrentState(integer $InstanceID)
``` 
Parameter _$InstanceID_: ObjectID of the Philips Hue Sync Box

**Change mode**
```php
HUESYNC_Mode(integer $InstanceID, string $mode)
``` 
Parameter _$InstanceID_: ObjectID of the Philips Hue Sync Box

Parameter _$mode_: passthrough | powersave | video | music | game

**Change brightnesse**
```php
HUESYNC_Brightness(integer $InstanceID, integer $brightness)
``` 
Parameter _$InstanceID_: ObjectID of the Philips Hue Sync Box

Parameter _$brightness_: 0 - 200

**Change intensity**
```php
HUESYNC_Intensity(integer $InstanceID, string $mode, string $intensity)
``` 
Parameter _$InstanceID_: ObjectID of the Philips Hue Sync Box

Parameter _$mode_: passthrough | powersave | video | music | game

Parameter _$intensity_: subtle | moderate | high | intense

**ARC Bypass**
```php
HUESYNC_ARC_Bypass(integer $InstanceID)
``` 
Parameter _$InstanceID_: ObjektID der Philips Hue Sync Box Instanz.

**Auto switch inputs**
```php
HUESYNC_AutoSwitchInputs(integer $InstanceID, integer $input, boolean $state)
``` 
Parameter _$InstanceID_: ObjectID of the Philips Hue Sync Box

Parameter _$input_: 1 - 4 

Parameter _$state_: true | false 

**Auto sync inputs**
```php
HUESYNC_AutoSync(integer $InstanceID, integer $input, boolean $state)
``` 
Parameter _$InstanceID_: ObjektID der Philips Hue Sync Box Instanz.

Parameter _$input_: 1 - 4 

Parameter _$state_: true | false 

**CEC power state detection**
```php
HUESYNC_CEC_PowerStateDetection(integer $InstanceID, boolean $state)
``` 
Parameter _$InstanceID_: ObjectID of the Philips Hue Sync Box

Parameter _$state_: true | false

**Define controlled entertainment area**
```php
HUESYNC_DefineControlledEntertainmentArea(integer $InstanceID, string $area_id)
``` 
Parameter _$InstanceID_: ObjectID of the Philips Hue Sync Box

Parameter _$area_id_:  entertainment area id

**Define Input Names**
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
Parameter _$InstanceID_: ObjectID of the Philips Hue Sync Box

Parameter _$state_: true | false

**HDMI input detected**
```php
HUESYNC_HDMI_InputDetected(integer $InstanceID, boolean $state)
``` 
Parameter _$InstanceID_: ObjectID of the Philips Hue Sync Box

Parameter _$state_: true | false

**Power on Philips Hue Sync Box**
```php
HUESYNC_PowerOn(integer $InstanceID)
``` 
Parameter _$InstanceID_: ObjectID of the Philips Hue Sync Box

**Power off Philips Hue Sync Box**
```php
HUESYNC_PowerOff(integer $InstanceID)
``` 
Parameter _$InstanceID_: ObjectID of the Philips Hue Sync Box

**Power toggle Philips Hue Sync Box**
```php
HUESYNC_PowerToggle(integer $InstanceID)
``` 
Parameter _$InstanceID_: ObjectID of the Philips Hue Sync Box

**Restart Philips Hue Sync Box**
```php
HUESYNC_RestartSyncBox(integer $InstanceID)
``` 
Parameter _$InstanceID_: ObjectID of the Philips Hue Sync Box

**Toogle background lighting**
```php
HUESYNC_BackgroundLighting(integer $InstanceID, string $mode, boolean $state)
``` 
Parameter _$InstanceID_: ObjectID of the Philips Hue Sync Box

Parameter _$mode_: video | game

Parameter _$state_: true | false  

**USB power state detection**
```php
HUESYNC_USB_PowerStateDetection(integer $InstanceID, boolean $state)
``` 
Parameter _$InstanceID_: ObjectID of the Philips Hue Sync Box

Parameter _$state_: true | false

**LED Mode**
```php
HUESYNC_LEDMode(integer $InstanceID, int $mode)
``` 
Parameter _$InstanceID_: ObjectID of the Philips Hue Sync Box

Parameter _$mode_: 0 = off | 1 = regular | 2 = dimmed

**Previous SyncMode**
```php
HUESYNC_PreviousSyncMode(integer $InstanceID)
``` 
Parameter _$InstanceID_: ObjectID of the Philips Hue Sync Box

**Next SyncMode**
```php
HUESYNC_NextSyncMode(integer $InstanceID)
``` 
Parameter _$InstanceID_: ObjectID of the Philips Hue Sync Box

**Previous HDMI Source**
```php
HUESYNC_PreviousHDMISource(integer $InstanceID)
``` 
Parameter _$InstanceID_: ObjectID of the Philips Hue Sync Box

**Next SyncMode**
```php
HUESYNC_NextHDMISource(integer $InstanceID)
``` 
Parameter _$InstanceID_: ObjectID of the Philips Hue Sync Box

**Previous Intensity**
```php
HUESYNC_PreviousIntensity(integer $InstanceID)
``` 
Parameter _$InstanceID_: ObjectID of the Philips Hue Sync Box

**Next Intensity**
```php
HUESYNC_NextIntensity(integer $InstanceID)
``` 
Parameter _$InstanceID_: ObjectID of the Philips Hue Sync Box
**Palette**
```php
HUESYNC_Palette(integer $InstanceID, string $palette)
``` 
Parameter _$InstanceID_: ObjectID of the Philips Hue Sync Box

Parameter _$palette_: happyEnergetic, happyCalm, melancholicCalm, melancholic Energetic, neutral

###  b. GUIDs and data exchange:

#### Philips Hue Sync Box:

GUID: `{716FA5CE-2292-8EA5-78F9-8B245EFAF0A7}` 