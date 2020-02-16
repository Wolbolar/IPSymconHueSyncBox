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

## 2. Requirements

 - IPS > 5.1
 - Philips Hue Sync Box

## 3. Installation

### a. Loading the module

Open the IP Console's web console with _http://{IP-Symcon IP}:3777/console/_.

Then click on the module store icon in the upper right corner.

![Store](img/store_icon.png?raw=true "open store")

In the search field type

```
Hue Sync Box
```  


![Store](img/module_store_search_en.png?raw=true "module search")

Then select the module and click _Install_

![Store](img/install_en.png?raw=true "install")

### b.  Setup in IP-Symcon

In IP-Symcon select _Add instance_ (right click -> Add object -> Instance_) under the category under which you want to add the Philips Hue Sync Box,
and select _Philips Hue Sync Box_.

Then confirm with _Apply Changes_.

![Apply_Changes](img/apply_changes_en.png?raw=true "Adpply Changes")


## 4. Function reference

### Philips Hue Sync Box:

The EC-PMS2-LAN power strip has 4 ports which can be individually switched on / off.

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

**Restart Philips Hue Sync Box**
```php
HUESYNC_RestartSyncBox(integer $InstanceID)
``` 
Parameter _$InstanceID_: ObjektID der Philips Hue Sync Box Instanz.

**Toogle background lighting**
```php
HUESYNC_BackgroundLighting(integer $InstanceID, string $mode, boolean $state)
``` 
Parameter _$InstanceID_: ObjektID der Philips Hue Sync Box Instanz.

Parameter _$mode_: video | game

Parameter _$state_: true | false  

**USB power state detection**
```php
HUESYNC_USB_PowerStateDetection(integer $InstanceID, boolean $state)
``` 
Parameter _$InstanceID_: ObjectID of the Philips Hue Sync Box

Parameter _$state_: true | false

###  b. GUIDs and data exchange:

#### Philips Hue Sync Box:

GUID: `{716FA5CE-2292-8EA5-78F9-8B245EFAF0A7}` 