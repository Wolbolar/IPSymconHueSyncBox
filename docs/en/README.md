# IPSymconHueSyncBox
[![IP-Symcon Module](https://img.shields.io/badge/IP--Symcon-Module-blue.svg)](https://www.symcon.de/)
[![Symcon Version](https://img.shields.io/badge/Symcon-%3E%205.1-green.svg)](https://www.symcon.de/service/dokumentation/installation/)
[![PHP Version](https://img.shields.io/badge/PHP-%3E%207.4-blue.svg)](https://www.php.net/)
[![License](https://img.shields.io/badge/License-GPLv3-blue.svg)](https://www.gnu.org/licenses/gpl-3.0.html)

Philips Hue Sync Box module for IP-Symcon.

Enables remote control and status retrieval of a Philips Hue Sync Box within IP-Symcon.

---

## Documentation

**Table of Contents**

1. [Features](#1-features)
2. [Requirements](#2-requirements)
3. [Installation](#3-installation)
4. [Function Reference](#4-function-reference)
5. [Configuration](#5-configuration)
6. [Annex](#6-annex)

## 1. Features

The Philips Hue Sync Box allows synchronization of Philips Hue lamps with an incoming video or audio signal.
This module enables remote control of the Philips Hue Sync Box from IP-Symcon and displays the current device state within IP-Symcon.

You can configure:
- Synchronization mode
- Intensity
- Brightness
- HDMI input
- Power on / off
- Advanced settings

## 2. Requirements

- IPS > 5.1
- Philips Hue Sync Box

## 3. Installation

### a. Set up the Sync Box with the official Hue Sync iOS/Android app

First, set up the Philips Hue Sync Box using the official Hue Sync iOS/Android app.
After the initial setup, ensure that the firmware is updated to the latest version.

### b. Loading the module

Open the IP-Symcon Web Console via:

_http://{IP-Symcon IP}:3777/console/_

Click the module store icon in the upper right corner.

![Store](img/store_icon.png?raw=true "open store")

In the search field, enter:

```
Philips Hue Sync Box
```

![Store](img/module_store_search_en.png?raw=true "module search")

Select the module and click **_Install_**.

![Store](img/install_en.png?raw=true "install")

### c. Setup in IP-Symcon

A discovery instance for the Philips Hue Sync Box is created automatically. If IP-Symcon is located in the same network as the Philips Hue Sync Box, it will be detected and can be created using **_Create_**.
An instance of the Philips Hue Sync Box will then be generated in IP-Symcon.

#### Registration of the Hue Sync Box

To connect IP-Symcon with the Hue Sync Box, please follow these steps:

1. In the instance, click **_Registration_**.
2. A popup window will open.
3. Within 5 seconds, press and hold the button on the Hue Sync Box for approximately 3 seconds until the LED blinks green, then release it.
4. The module will automatically attempt to obtain an access token.

If the registration is not successful, the process can be restarted from the popup using **_Retry_**.

After successful registration, the access token is stored and the extended settings of the Hue Sync Box become available.

In the instance, you can choose to automatically create additional scripts.

![huescript](img/hue_sync_scripts.png?raw=true "Webfront")

Scripts for important functions will then be generated automatically.

Additional variables can optionally be selected under **_Properties_** for display in the WebFront.

## 4. Function Reference

### WebFront View

![webfront](img/hue_sync_webfront.png?raw=true "Webfront")

### Possible View in NEO

![viewneo](img/hue_sync_box_neo.png?raw=true "View NEO")

### Philips Hue Sync Box

The Hue Sync Box can be switched on or off, the input can be changed, and the synchronization mode and intensity can be adjusted.

## 5. Configuration

### Philips Hue Sync Box

| Property       | Type    | Value | Description              |
| :------------: | :-----: | :---: | :----------------------- |
| Host           | string  |       | IP address               |
| UpdateInterval | integer |       | Update interval in seconds |

## 6. Annex

### a. Functions

#### Philips Hue Sync Box

**Get basic device information**
```php
HUESYNC_GetDeviceInfo(integer $InstanceID)
```
Parameter _$InstanceID_: ObjectID of the Philips Hue Sync Box.

**Register IP-Symcon on the Philips Hue Sync Box**
```php
HUESYNC_Registration(integer $InstanceID)
```
Parameter _$InstanceID_: ObjectID of the Philips Hue Sync Box.

#### The following methods only work after IP-Symcon has been successfully registered on the Hue Sync Box

**Get current state**
```php
HUESYNC_GetCurrentState(integer $InstanceID)
```
Parameter _$InstanceID_: ObjectID of the Philips Hue Sync Box.

**Change mode**
```php
HUESYNC_Mode(integer $InstanceID, string $mode)
```
Parameter _$InstanceID_: ObjectID of the Philips Hue Sync Box.

Parameter _$mode_: passthrough | powersave | video | music | game

**Change brightness**
```php
HUESYNC_Brightness(integer $InstanceID, integer $brightness)
```
Parameter _$InstanceID_: ObjectID of the Philips Hue Sync Box.

Parameter _$brightness_: 0 - 200

**Change intensity**
```php
HUESYNC_Intensity(integer $InstanceID, string $mode, string $intensity)
```
Parameter _$InstanceID_: ObjectID of the Philips Hue Sync Box.

Parameter _$mode_: video | music | game

Parameter _$intensity_: subtle | moderate | high | intense

**ARC Bypass**
```php
HUESYNC_ARC_Bypass(integer $InstanceID)
```
Parameter _$InstanceID_: ObjectID of the Philips Hue Sync Box.

**Auto switch inputs**
```php
HUESYNC_AutoSwitchInputs(integer $InstanceID, integer $input, boolean $state)
```
Parameter _$InstanceID_: ObjectID of the Philips Hue Sync Box.

Parameter _$input_: 1 - 4

Parameter _$state_: true | false

**Auto sync inputs**
```php
HUESYNC_AutoSync(integer $InstanceID, integer $input, boolean $state)
```
Parameter _$InstanceID_: ObjectID of the Philips Hue Sync Box.

Parameter _$input_: 1 - 4

Parameter _$state_: true | false

**CEC power state detection**
```php
HUESYNC_CEC_PowerStateDetection(integer $InstanceID, boolean $state)
```
Parameter _$InstanceID_: ObjectID of the Philips Hue Sync Box.

Parameter _$state_: true | false

**Define controlled entertainment area**
```php
HUESYNC_DefineControlledEntertainmentArea(integer $InstanceID, string $area_id)
```
Parameter _$InstanceID_: ObjectID of the Philips Hue Sync Box.

Parameter _$area_id_: Entertainment area ID

**Define input names**
```php
HUESYNC_DefineInputNames(integer $InstanceID, string $name1, string $name2, string $name3, string $name4)
```
Parameter _$InstanceID_: ObjectID of the Philips Hue Sync Box.

Parameter _$name1_: Input name for HDMI 1
Parameter _$name2_: Input name for HDMI 2
Parameter _$name3_: Input name for HDMI 3
Parameter _$name4_: Input name for HDMI 4

**HDMI inactivity power state**
```php
HUESYNC_HDMI_InactivityPowerState(integer $InstanceID, boolean $state)
```
Parameter _$InstanceID_: ObjectID of the Philips Hue Sync Box.

Parameter _$state_: true | false

**HDMI input detected**
```php
HUESYNC_HDMI_InputDetected(integer $InstanceID, boolean $state)
```
Parameter _$InstanceID_: ObjectID of the Philips Hue Sync Box.

Parameter _$state_: true | false

**Power on Philips Hue Sync Box**
```php
HUESYNC_PowerOn(integer $InstanceID)
```
Parameter _$InstanceID_: ObjectID of the Philips Hue Sync Box.

**Power off Philips Hue Sync Box**
```php
HUESYNC_PowerOff(integer $InstanceID)
```
Parameter _$InstanceID_: ObjectID of the Philips Hue Sync Box.

**Power toggle Philips Hue Sync Box**
```php
HUESYNC_PowerToggle(integer $InstanceID)
```
Parameter _$InstanceID_: ObjectID of the Philips Hue Sync Box.

**Restart Philips Hue Sync Box**
```php
HUESYNC_RestartSyncBox(integer $InstanceID)
```
Parameter _$InstanceID_: ObjectID of the Philips Hue Sync Box.

**Toggle background lighting**
```php
HUESYNC_BackgroundLighting(integer $InstanceID, string $mode, boolean $state)
```
Parameter _$InstanceID_: ObjectID of the Philips Hue Sync Box.

Parameter _$mode_: video | game
Parameter _$state_: true | false

**USB power state detection**
```php
HUESYNC_USB_PowerStateDetection(integer $InstanceID, boolean $state)
```
Parameter _$InstanceID_: ObjectID of the Philips Hue Sync Box.

Parameter _$state_: true | false

**LED mode**
```php
HUESYNC_LEDMode(integer $InstanceID, int $mode)
```
Parameter _$InstanceID_: ObjectID of the Philips Hue Sync Box.

Parameter _$mode_: 0 = off | 1 = regular | 2 = dimmed

**Previous SyncMode**
```php
HUESYNC_PreviousSyncMode(integer $InstanceID)
```
Parameter _$InstanceID_: ObjectID of the Philips Hue Sync Box.

**Next SyncMode**
```php
HUESYNC_NextSyncMode(integer $InstanceID)
```
Parameter _$InstanceID_: ObjectID of the Philips Hue Sync Box.

**Previous HDMI source**
```php
HUESYNC_PreviousHDMISource(integer $InstanceID)
```
Parameter _$InstanceID_: ObjectID of the Philips Hue Sync Box.

**Next HDMI source**
```php
HUESYNC_NextHDMISource(integer $InstanceID)
```
Parameter _$InstanceID_: ObjectID of the Philips Hue Sync Box.

**Previous intensity**
```php
HUESYNC_PreviousIntensity(integer $InstanceID)
```
Parameter _$InstanceID_: ObjectID of the Philips Hue Sync Box.

**Next intensity**
```php
HUESYNC_NextIntensity(integer $InstanceID)
```
Parameter _$InstanceID_: ObjectID of the Philips Hue Sync Box.

**Palette**
```php
HUESYNC_Palette(integer $InstanceID, string $palette)
```
Parameter _$InstanceID_: ObjectID of the Philips Hue Sync Box.

Parameter _$palette_: happyEnergetic | happyCalm | melancholicCalm | melancholicEnergetic | neutral

### b. GUIDs and Data Exchange

#### Philips Hue Sync Box

GUID: `{716FA5CE-2292-8EA5-78F9-8B245EFAF0A7}`