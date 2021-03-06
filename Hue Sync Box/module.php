<?php
declare(strict_types=1);

require_once __DIR__ . '/../libs/ProfileHelper.php';
require_once __DIR__ . '/../libs/ConstHelper.php';

class HueSyncBox extends IPSModule
{

    use ProfileHelper;

    // helper properties
    private $position = 0;

    private const APPSECRET  = 'MTIzNDU2Nzg5MDEyMzQ1Njc4OTAxMjM0NTY3ODkwMTI=';
    private const APIPATH    = '/api/v1';
    private const Video_Mode = 2;
    private const Music_Mode = 3;
    private const Game_Mode  = 4;


    public function Create()
    {
        //Never delete this line!
        parent::Create();

        $this->RegisterPropertyInteger("UpdateInterval", 60);
        $this->RegisterTimer("Update", 0, "HUESYNC_Update(" . $this->InstanceID . ");");
        $this->RegisterPropertyString('Host', '');
        $this->RegisterPropertyString('deviceType', '');
        $this->RegisterPropertyString('huesync_name', '');
        $this->RegisterPropertyString('huesync_uniqueId', '');
        $this->RegisterPropertyString('app_name', 'Symcon');
        $this->RegisterPropertyString('instance_name', 'Symcon');
        $this->RegisterAttributeString('AccessToken', '');
        $this->RegisterAttributeInteger('registrationId', 0);
        $this->RegisterAttributeString('name', ''); // Friendly name of the device
        $this->RegisterAttributeString('deviceType', ''); // Device Type identifier – currently fixed to HSB1
        $this->RegisterAttributeString('uniqueId', ''); // Capitalized hex string of the 6 byte / 12 characters device id without delimiters. Used as unique id on label, certificate common name, hostname etc.
        $this->RegisterAttributeString('ipAddress', ''); // Local IP address of the device
        $this->RegisterAttributeInteger('apiLevel', 0); // Increased between firmware versions when api changes. Only apiLevel >= 4 is supported.
        $this->RegisterAttributeString('firmwareVersion', ''); // User readable version of the device firmware, starting with decimal major .minor .maintenance format e.g. “1.12.3”
        $this->RegisterAttributeInteger('buildNumber', 0); // Build number of the firmware. Unique for every build with newer builds guaranteed a higher number than older.
        $this->RegisterAttributeString('lastCheckedUpdate', ''); // UTC time when last check for update was performed.
        $this->RegisterAttributeString('updatableBuildNumber', ''); // Build number that is available to update to. Item is set to null when there is no update available.
        $this->RegisterAttributeString('updatableFirmwareVersion', ''); // User readable version of the firmware the device can upgrade to. Item is set to null when there is no update available.
        $this->RegisterAttributeBoolean('autoUpdateEnabled', false); // Sync Box checks daily for a firmware update. If true, an available update will automatically be installed. This will be postponed if Sync Box is passing through content to the TV and being used.
        $this->RegisterAttributeInteger('autoUpdateTime', 0); // UTC hour when the automatic update will check and execute, values 0 – 23. Default is 10. Ideally this value should be set to 3AM according to user’s timezone.
        $this->RegisterAttributeInteger('ledMode', 1); // 1 = regular; 0 = off in powersave, passthrough or sync mode; 2 = dimmed in powersave or passthrough mode and off in sync mode
        $this->RegisterAttributeString('wifiState', 'uninitialized'); // uninitialized, disconnected, lan, wan
        $this->RegisterAttributeBoolean('termsAgreed', true);
        $this->RegisterAttributeString('device_action', ''); // none, doSoftwareRestart,  doFirmwareUpdate
        $this->RegisterAttributeInteger('maxIrCodes', 16); // The total number of IR codes configurable
        $this->RegisterAttributeInteger('maxPresets', 16); // The total number of Presets configurable
        $this->RegisterAttributeString('hue_bridgeUniqueId', ''); // 16 character ascii hex string bridge identifier
        $this->RegisterAttributeString('hue_bridgeIpAddress', ''); // Readable, dot IPv4 address of the paired bridge EG “192.168.1.50”
        $this->RegisterAttributeString('hue_groupId', '');
        $this->RegisterAttributeString('hue_groups', '[]'); // All available entertainment areas on the current bridge. When this object is not available, it means the bridge groups have not been retrieved yet. When the object is empty, it means there are no entertainment areas on the bridge. When the bridge connection is lost, the last known values are remembered. Determining whether values may be outdated can be done based on connectionState.
        $this->RegisterAttributeString('hue_connectionState', '');
        $this->RegisterAttributeBoolean('syncActive', false); // Reports false in case of powersave or passthrough mode, and true in case of video, game, music, or ambient mode. When changed from false to true, it will start syncing in last used mode for current source. Requires hue /connectionState to be connected. When changed from true to false, will set passthrough mode.
        $this->RegisterAttributeBoolean('hdmiActive', false); // Reports false in case of powersave mode, and true in case of passthrough, video, game, music or ambient mode. When changed from false to true, it will set passthrough mode. When changed from true to false, will set powersave mode.
        $this->RegisterAttributeString('lastSyncMode', 'video'); // video, game, music, ambient
        $this->RegisterAttributeString('video', '[]');
        $this->RegisterAttributeString('video_intensity', ''); // subtle, moderate, high, intense
        $this->RegisterAttributeBoolean('backlight_video', false);
        $this->RegisterAttributeBoolean('backlight_video_enabled', false);
        $this->RegisterAttributeString('game', '[]');
        $this->RegisterAttributeString('game_intensity', ''); // subtle, moderate, high, intense
        $this->RegisterAttributeBoolean('backlight_game', false);
        $this->RegisterAttributeBoolean('backlight_game_enabled', false);
        $this->RegisterAttributeString('music', '[]');
        $this->RegisterAttributeString('music_intensity', ''); // subtle, moderate, high, intense
        $this->RegisterAttributeString('music_palette', ''); // happyEnergetic, happyCalm, melancholicCalm, melancholic Energetic, neutral
        $this->RegisterAttributeString('preset', '');
        $this->RegisterAttributeString('ambient', '[]'); // Deprecated, will be removed in a future release
        $this->RegisterAttributeString('input1_name', ''); // Friendly name, not empty
        $this->RegisterAttributeBoolean('input1_name_enabled', false);
        $this->RegisterAttributeString('input1_type', ''); // Friendly type: generic, video, game, music, xbox, playstation, nintendoswitch, phone, desktop, laptop, appletv, roku, shield, chromecast, firetv, diskplayer, settopbox, satellite, avreceiver, soundbar, hdmiswitch
        $this->RegisterAttributeString('input1_status', ''); // unplugged, plugged, linked, unknown
        $this->RegisterAttributeString('input1_lastSyncMode', ''); // video, game, music
        $this->RegisterAttributeString('input2_name', ''); // Friendly name, not empty
        $this->RegisterAttributeBoolean('input2_name_enabled', false);
        $this->RegisterAttributeString('input2_type', ''); // Friendly type: generic, video, game, music, xbox, playstation, nintendoswitch, phone, desktop, laptop, appletv, roku, shield, chromecast, firetv, diskplayer, settopbox, satellite, avreceiver, soundbar, hdmiswitch
        $this->RegisterAttributeString('input2_status', ''); // unplugged, plugged, linked, unknown
        $this->RegisterAttributeString('input2_lastSyncMode', ''); // video, game, music
        $this->RegisterAttributeString('input3_name', ''); // Friendly name, not empty
        $this->RegisterAttributeBoolean('input3_name_enabled', false);
        $this->RegisterAttributeString('input3_type', ''); // Friendly type: generic, video, game, music, xbox, playstation, nintendoswitch, phone, desktop, laptop, appletv, roku, shield, chromecast, firetv, diskplayer, settopbox, satellite, avreceiver, soundbar, hdmiswitch
        $this->RegisterAttributeString('input3_status', ''); // unplugged, plugged, linked, unknown
        $this->RegisterAttributeString('input3_lastSyncMode', ''); // video, game, music
        $this->RegisterAttributeString('input4_name', ''); // Friendly name, not empty
        $this->RegisterAttributeBoolean('input4_name_enabled', false);
        $this->RegisterAttributeString('input4_type', ''); // Friendly type: generic, video, game, music, xbox, playstation, nintendoswitch, phone, desktop, laptop, appletv, roku, shield, chromecast, firetv, diskplayer, settopbox, satellite, avreceiver, soundbar, hdmiswitch
        $this->RegisterAttributeString('input4_status', ''); // unplugged, plugged, linked, unknown
        $this->RegisterAttributeString('input4_lastSyncMode', ''); // video, game, music
        $this->RegisterAttributeString('output_name', '');
        $this->RegisterAttributeString('output_type', '');
        $this->RegisterAttributeString('output_status', ''); // unplugged, plugged, linked, unknown
        $this->RegisterAttributeString('output_lastSyncMode', '');
        $this->RegisterAttributeString('contentSpecs', ''); // <horizontal pixels> x <vertical pixels> @ <framerate fpks> – <HDR>
        $this->RegisterAttributeBoolean('videoSyncSupported', true); // Current content specs supported for video sync (video/game mode)
        $this->RegisterAttributeBoolean('audioSyncSupported', true); // Current content specs supported for audio sync (music mode)
        $this->RegisterAttributeInteger('inactivePowersave', 20); // Device automatically goes to powersave after this many minutes of being in passthrough mode with no link on any source or no link on output. 0 is disabled, max is 10000. Default: 20.
        $this->RegisterAttributeBoolean('inactivePowersave_enabled', false);
        $this->RegisterAttributeInteger('cecPowersave', 1); // Device goes to powersave when TV sends CEC OFF. Default: 1. Disabled 0, Enabled 1.
        $this->RegisterAttributeBoolean('cecPowersave_enabled', false);
        $this->RegisterAttributeInteger('usbPowersave', 1); // Device goes to powersave when USB power transitions from 5V to 0V. Default: 1. Disabled 0, Enabled 1.
        $this->RegisterAttributeBoolean('usbPowersave_enabled', false);
        $this->RegisterAttributeInteger('hpdInputSwitch', 1);
        $this->RegisterAttributeInteger('arcBypassMode', 0);
        $this->RegisterAttributeBoolean('arcBypassMode_enabled', false);
        $this->RegisterAttributeInteger('forceDoviNative', 0); // When the TV advertises Dolby Vision force to use native native mode. Disabled 0, Enabled 1.
        $this->RegisterAttributeBoolean('forceDoviNative_enabled', false);
        $this->RegisterAttributeInteger('input1_cecInputSwitch', 1); // Automatically switch input when this source sends CEC active. Default: 1. Disabled 0, Enabled 1.
        $this->RegisterAttributeInteger('input1_linkAutoSync', 0); // Automatically set syncActive true when this source and output are linked. Default: 0. Disabled 0, Enabled 1.
        $this->RegisterAttributeInteger('input2_cecInputSwitch', 1); // Automatically switch input when this source sends CEC active. Default: 1. Disabled 0, Enabled 1.
        $this->RegisterAttributeInteger('input2_linkAutoSync', 0); // Automatically set syncActive true when this source and output are linked. Default: 0. Disabled 0, Enabled 1.
        $this->RegisterAttributeInteger('input3_cecInputSwitch', 1); // Automatically switch input when this source sends CEC active. Default: 1. Disabled 0, Enabled 1.
        $this->RegisterAttributeInteger('input3_linkAutoSync', 0); // Automatically set syncActive true when this source and output are linked. Default: 0. Disabled 0, Enabled 1.
        $this->RegisterAttributeInteger('input4_cecInputSwitch', 1); // Automatically switch input when this source sends CEC active. Default: 1. Disabled 0, Enabled 1.
        $this->RegisterAttributeInteger('input4_linkAutoSync', 0); // Automatically set syncActive true when this source and output are linked. Default: 0. Disabled 0, Enabled 1.
        $this->RegisterAttributeBoolean('scanning', false); // Scanning mode causes the last-received IR code to be saved (instead of processing), displayed as the ‘code’ attribute. Scanning automatically deactivates after 20 seconds but can continually be enabled again without gaps. After scanning an IR code, scanning will immediately be disabled.
        $this->RegisterAttributeString('code', ''); // The last scanned code received while in scanning mode. Value is null if not scanned.
        $this->RegisterAttributeString('codes', '[]');
        $this->RegisterAttributeString('registrations', '[]');
        $this->RegisterAttributeInteger('Brightness', 0); // 0 – 200 (100 = no brightness reduction/boost compared to input, 0 = max reduction, 200 = max boost)
        $this->RegisterAttributeString('hueTarget', ''); // groups/<groupId> (currently selected entertainment area)
        $this->RegisterAttributeInteger('Input', 0);
        $this->RegisterAttributeInteger('Mode', 0);
        $this->RegisterAttributeInteger('Intensity', 0);
        $this->RegisterAttributeBoolean('State', false);
        $this->RegisterPropertyInteger('ImportCategoryID', 0);
        $this->RegisterPropertyBoolean('HueSyncScript', false);
        $this->RegisterAttributeBoolean('AlexaVoiceControl', false);
        $this->RegisterAttributeBoolean('GoogleVoiceControl', false);
        $this->RegisterAttributeBoolean('SiriVoiceControl', false);
        $this->RegisterAttributeString('presets', '[]');

        //we will wait until the kernel is ready
        $this->RegisterMessage(0, IPS_KERNELMESSAGE);
    }

    public function Destroy()
    {
        //Never delete this line!
        parent::Destroy();
    }

    public function ApplyChanges()
    {
        //Never delete this line!
        parent::ApplyChanges();

        if (IPS_GetKernelRunlevel() !== KR_READY) {
            return;
        }

        if (!$this->ValidateConfiguration()) {
            return;
        }


    }

    protected function SetHUESyncTimerInterval()
    {
        $update_interval = $this->ReadPropertyInteger('UpdateInterval');
        $Interval        = $update_interval * 1000;
        $this->SetTimerInterval("Update", $Interval);
    }

    /** @noinspection PhpMissingParentCallCommonInspection */
    public function MessageSink($TimeStamp, $SenderID, $Message, $Data)
    {

        switch ($Message) {
            case IM_CHANGESTATUS:
                if ($Data[0] === IS_ACTIVE) {
                    $this->ApplyChanges();
                }
                break;

            case IPS_KERNELMESSAGE:
                if ($Data[0] === KR_READY) {
                    $this->ApplyChanges();
                }
                break;

            default:
                break;
        }
    }

    private function ValidateConfiguration(): bool
    {
        $this->SetupVariables();
        $host         = $this->ReadPropertyString('Host');
        $access_token = $this->ReadAttributeString('AccessToken');
        //IP prüfen
        if (!filter_var($host, FILTER_VALIDATE_IP) === false) {
            //IP ok
            $ipcheck = true;
            $this->GetDeviceInfo();
        } else {
            $ipcheck = false;
            $this->SendDebug('Hue Sync Box', 'ip check Hue Sync Box failed', 0);
        }
        if ($ipcheck === true && $access_token != '') {
            $this->SetHUESyncTimerInterval();
        }
        if ($ipcheck === false) {
            $this->SendDebug('Hue Sync Box', 'Hue Sync Box host not valid', 0);
            $this->SetStatus(218); //IP Adresse oder Host ist ungültig
        } else {
            $huescript = $this->ReadPropertyBoolean('HueSyncScript');
            if($huescript)
            {
                $this->SetupHueSyncScripts();
            }
            $this->SetStatus(IS_ACTIVE);
            return true;
        }
        return false;
    }

    protected function SetupVariables()
    {
        $this->RegisterProfile('Hue.Sync.Brightness', 'Intensity', '', ' %', 0, 200, 1, 0, VARIABLETYPE_INTEGER);
        $this->SetupVariable(
            'Brightness', $this->Translate('Brightness'), 'Hue.Sync.Brightness', $this->_getPosition(), VARIABLETYPE_INTEGER, true, true
        );
        $input_ass = [
            [0, $this->Translate("HDMI 1"), "", -1],
            [1, $this->Translate("HDMI 2"), "", -1],
            [2, $this->Translate("HDMI 3"), "", -1],
            [3, $this->Translate("HDMI 4"), "", -1]];
        $this->RegisterProfileAssociation("Hue.Sync.Input", "Execute", "", "", 0, 3, 0, 0, VARIABLETYPE_INTEGER, $input_ass);
        $this->SetupVariable(
            'Input', $this->Translate('Input'), 'Hue.Sync.Input', $this->_getPosition(), VARIABLETYPE_INTEGER, true, true
        );
        $mode_ass = [
            [0, $this->Translate("Passthrough"), "", -1],
            [1, $this->Translate("Powersave"), "", -1],
            [2, $this->Translate("Video"), "", -1],
            [3, $this->Translate("Music"), "", -1],
            [4, $this->Translate("Game"), "", -1]];
        $this->RegisterProfileAssociation("Hue.Sync.Mode", "Execute", "", "", 0, 4, 0, 0, VARIABLETYPE_INTEGER, $mode_ass);
        $this->SetupVariable(
            'Mode', $this->Translate('Mode'), 'Hue.Sync.Mode', $this->_getPosition(), VARIABLETYPE_INTEGER, true, true
        );
        $intensity_ass = [
            [0, $this->Translate("subtle"), "", -1],
            [1, $this->Translate("moderate"), "", -1],
            [2, $this->Translate("high"), "", -1],
            [3, $this->Translate("intense"), "", -1]];
        $this->RegisterProfileAssociation("Hue.Sync.Intensity", "Execute", "", "", 0, 3, 0, 0, VARIABLETYPE_INTEGER, $intensity_ass);
        $this->SetupVariable(
            'Intensity', $this->Translate('Intensity'), 'Hue.Sync.Intensity', $this->_getPosition(), VARIABLETYPE_INTEGER, true, true
        );
        $palette_ass = [
            [0, $this->Translate("happy energetic"), "", -1],
            [1, $this->Translate("happy calm"), "", -1],
            [2, $this->Translate("melancholic calm"), "", -1],
            [3, $this->Translate("melancholic energetic"), "", -1],
            [4, $this->Translate("neutral"), "", -1]];
        $this->RegisterProfileAssociation("Hue.Sync.Palette", "Execute", "", "", 0, 4, 0, 0, VARIABLETYPE_INTEGER, $palette_ass);
        $this->SetupVariable(
            'music_palette', $this->Translate('Palette'), 'Hue.Sync.Palette', $this->_getPosition(), VARIABLETYPE_INTEGER, true, true
        );

        $this->SetupVariable(
            'firmwareVersion', $this->Translate('Firmware'), '', $this->_getPosition(), VARIABLETYPE_STRING, false, true
        );
        $this->SetupVariable(
            'State', $this->Translate('State'), '~Switch', $this->_getPosition(), VARIABLETYPE_BOOLEAN, true, true
        );
        // 1 = regular; 0 = off in powersave, passthrough or sync mode; 2 = dimmed in powersave or passthrough mode and off in sync mode
        $ledmode_ass = [
            [0, $this->Translate("off"), "", -1],
            [1, $this->Translate("regular"), "", -1],
            [2, $this->Translate("dimmed"), "", -1]];
        $this->RegisterProfileAssociation("Hue.Sync.LED_Mode", "Execute", "", "", 0, 2, 0, 0, VARIABLETYPE_INTEGER, $ledmode_ass);
        $this->SetupVariable(
            'ledMode', $this->Translate('LED Mode'), 'Hue.Sync.LED_Mode', $this->_getPosition(), VARIABLETYPE_INTEGER, true, true
        );
        $this->SetupVariable(
            'syncActive', $this->Translate('Sync Active'), '~Switch', $this->_getPosition(), VARIABLETYPE_BOOLEAN, true, true
        );
        $this->SetupVariable(
            'hdmiActive', $this->Translate('HDMI Active'), '~Switch', $this->_getPosition(), VARIABLETYPE_BOOLEAN, false, true
        );
        $this->SetupVariable(
            'input1_name', $this->Translate('HDMI Input 1 Name'), '', $this->_getPosition(), VARIABLETYPE_STRING, false, false
        );
        $this->SetupVariable(
            'input2_name', $this->Translate('HDMI Input 2 Name'), '', $this->_getPosition(), VARIABLETYPE_STRING, false, false
        );
        $this->SetupVariable(
            'input3_name', $this->Translate('HDMI Input 3 Name'), '', $this->_getPosition(), VARIABLETYPE_STRING, false, false
        );
        $this->SetupVariable(
            'input4_name', $this->Translate('HDMI Input 4 Name'), '', $this->_getPosition(), VARIABLETYPE_STRING, false, false
        );
        $this->RegisterProfile('Hue.Sync.Powersave', 'Intensity', '', ' min', 0, 10000, 1, 0, VARIABLETYPE_INTEGER);
        $this->SetupVariable(
            'inactivePowersave', $this->Translate('HDMI Inactivity Power State'), 'Hue.Sync.Powersave', $this->_getPosition(), VARIABLETYPE_INTEGER, true, false
        );
        $this->SetupVariable(
            'cecPowersave', $this->Translate('CEC Powersave'), '~Switch', $this->_getPosition(), VARIABLETYPE_BOOLEAN, true, false
        );
        $this->SetupVariable(
            'usbPowersave', $this->Translate('USB Powersave'), '~Switch', $this->_getPosition(), VARIABLETYPE_BOOLEAN, true, false
        );
        $this->SetupVariable(
            'backlight_video', $this->Translate('Backlight Video'), '~Switch', $this->_getPosition(), VARIABLETYPE_BOOLEAN, true, false
        );
        $this->SetupVariable(
            'backlight_game', $this->Translate('Backlight Game'), '~Switch', $this->_getPosition(), VARIABLETYPE_BOOLEAN, true, false
        );

        $this->SetupVariable(
            'arcBypassMode', $this->Translate('ARC bypass'), '~Switch', $this->_getPosition(), VARIABLETYPE_BOOLEAN, true, false
        );

    }

    /** Variable anlegen / löschen
     *
     * @param $ident
     * @param $name
     * @param $profile
     * @param $position
     * @param $vartype
     * @param $visible
     *
     * @return bool|int
     */
    protected function SetupVariable($ident, $name, $profile, $position, $vartype, $enableaction, $visible = false)
    {
        $objid = false;
        if ($visible) {
            $this->SendDebug('Hue Sync Variable:', 'Variable with Ident ' . $ident . ' is visible', 0);
        } else {
            $visible = $this->ReadAttributeBoolean($ident . '_enabled');
            $this->SendDebug('Hue Sync Variable:', 'Variable with Ident ' . $ident . ' is shown' . print_r($visible, true), 0);
        }
        if ($visible == true) {
            switch ($vartype) {
                case VARIABLETYPE_BOOLEAN:
                    $objid = $this->RegisterVariableBoolean($ident, $name, $profile, $position);
                    if ($ident == 'cecPowersave' || $ident == 'usbPowersave' || $ident == 'arcBypassMode') {
                        $value = boolval($this->ReadAttributeInteger($ident));
                    } else {
                        $value = $this->ReadAttributeBoolean($ident);
                    }
                    $this->SetValue($ident, $value);
                    break;
                case VARIABLETYPE_INTEGER:
                    $objid = $this->RegisterVariableInteger($ident, $name, $profile, $position);
                    if ($ident == 'music_palette') {
                        $value = $this->GetPaletteValue($this->ReadAttributeString($ident));
                    } else {
                        $value = $this->ReadAttributeInteger($ident);
                    }
                    $this->SetValue($ident, $value);
                    break;
                case VARIABLETYPE_FLOAT:
                    $objid = $this->RegisterVariableFloat($ident, $name, $profile, $position);
                    break;
                case VARIABLETYPE_STRING:
                    $objid = $this->RegisterVariableString($ident, $name, $profile, $position);
                    $value = $this->ReadAttributeString($ident);
                    $this->SetValue($ident, $value);
                    break;
            }
            if ($enableaction) {
                $this->EnableAction($ident);
            }
        } else {
            $objid = @$this->GetIDForIdent($ident);
            if ($objid > 0) {
                $this->UnregisterVariable($ident);
            }
        }
        return $objid;
    }

    public function SetWebFrontVariable(string $ident, bool $value)
    {
        $this->WriteAttributeBoolean($ident, $value);
        $this->SetupVariables();
    }

    /** video, game, music, ambient
     * @return string
     */
    public function GetLastSyncMode()
    {
        $lastSyncMode = $this->ReadAttributeString('lastSyncMode');
        return $lastSyncMode;
    }

    public function Update()
    {
        $this->GetCurrentState();
    }

    /** Device info
     *
     * @return mixed
     */
    public function GetDeviceInfo()
    {
        $device_info = $this->SendDevice();

        $name = $device_info->name;
        $this->WriteAttributeString('name', $name); // Friendly name of the device
        $deviceType = $device_info->deviceType;
        $this->WriteAttributeString('deviceType', $deviceType); // Device Type identifier – currently fixed to HSB1
        $uniqueId = $device_info->uniqueId;
        $this->WriteAttributeString('uniqueId', $uniqueId); // Capitalized hex string of the 6 byte / 12 characters device id without delimiters. Used as unique id on label, certificate common name, hostname etc.
        $ip = $device_info->ipAddress;
        $this->WriteAttributeString('ipAddress', $ip); // Local IP address of the device
        $apiLevel = $device_info->apiLevel;
        $this->WriteAttributeInteger('apiLevel', $apiLevel); // Increased between firmware versions when api changes. Only apiLevel >= 4 is supported.
        $firmwareVersion = $device_info->firmwareVersion;
        $this->WriteAttributeString('firmwareVersion', $firmwareVersion); // User readable version of the device firmware, starting with decimal major .minor .maintenance format e.g. “1.12.3”
        $this->SetValue('firmwareVersion', $firmwareVersion);
        $buildNumber = $device_info->buildNumber;
        $this->WriteAttributeInteger('buildNumber', $buildNumber); // Build number of the firmware. Unique for every build with newer builds guaranteed a higher number than older.
        if(property_exists($device_info,'lastCheckedUpdate'))
        {
            $lastCheckedUpdate = $device_info->lastCheckedUpdate;
            $this->WriteAttributeString('lastCheckedUpdate', $lastCheckedUpdate); // UTC time when last check for update was performed.
        }
        if(property_exists($device_info,'updatableBuildNumber'))
        {
            $updatableBuildNumber = $device_info->updatableBuildNumber;
            $this->WriteAttributeString('updatableBuildNumber', $updatableBuildNumber); // Build number that is available to update to. Item is set to null when there is no update available.
        }
        if(property_exists($device_info,'updatableFirmwareVersion'))
        {
            $updatableFirmwareVersion = $device_info->updatableFirmwareVersion;
            $this->WriteAttributeString('updatableFirmwareVersion', $updatableFirmwareVersion); // User readable version of the firmware the device can upgrade to. Item is set to null when there is no update available.
        }
        if(property_exists($device_info,'update'))
        {
            $autoUpdateEnabled = $device_info->update->autoUpdateEnabled; // update Root object for automatic update configuration
            $this->WriteAttributeBoolean('autoUpdateEnabled', $autoUpdateEnabled); // Sync Box checks daily for a firmware update. If true, an available update will automatically be installed. This will be postponed if Sync Box is passing through content to the TV and being used.
            $autoUpdateTime = $device_info->update->autoUpdateTime; // update Root object for automatic update configuration
            $this->WriteAttributeInteger('autoUpdateTime', $autoUpdateTime); // UTC hour when the automatic update will check and execute, values 0 – 23. Default is 10. Ideally this value should be set to 3AM according to user’s timezone.
        }
        if(property_exists($device_info,'ledMode'))
        {
            $ledMode = $device_info->ledMode;
            $this->WriteAttributeInteger('ledMode', $ledMode); // 1 = regular; 0 = off in powersave, passthrough or sync mode; 2 = dimmed in powersave or passthrough mode and off in sync mode
            $this->SetValue('ledMode', $ledMode);
        }
        if(property_exists($device_info,'wifiState'))
        {
            $wifiState = $device_info->wifiState;
            $this->WriteAttributeString('wifiState', $wifiState); // uninitialized, disconnected, lan, wan
        }
        if(property_exists($device_info,'lastCheckedUpdate'))
        {
            $termsAgreed = $device_info->lastCheckedUpdate;
            $this->WriteAttributeBoolean('termsAgreed', $termsAgreed);
        }
        return $device_info;
    }

    /** Registration
     *
     * @return array
     */
    public function Registration()
    {
        $postfields       = [
            'appName'      => $this->ReadPropertyString('app_name'),
            'appSecret'    => self::APPSECRET,
            'instanceName' => $this->ReadPropertyString('instance_name')];
        $response = $this->SendCommand('/api/v1/registrations', 'POST', $postfields);
        if(isset($response['body']))
        {
            if($response['body'] == '{"code":16,"message":"Invalid State"}')
            {
                $this->SendDebug('Hue Sync Box', $this->Translate('device button is not yet pressed'), 0);
                $this->SendDebug('Hue Sync Box', $this->Translate('Within 5 seconds of the response, hold the device button until the led blinks green (~3 seconds) and release.'), 0);
                $this->SendDebug('Hue Sync Box', $this->Translate('Within 5 seconds of releasing, send the registration request again'), 0);
                return [];
            }

            $data             = json_decode($response['body'], true);
            if (isset($data['registrationId']) && isset($data['accessToken'])) {
                $access_token   = $data['accessToken'];
                $registrationId = intval($data['registrationId']);
                $this->WriteAccessToken($access_token, $registrationId);
            }
            return $response;
        }
        else
        {
            return [];
        }
    }

    public function WriteAccessToken(string $access_token, int $registrationId)
    {
        $this->WriteAttributeString('AccessToken', $access_token);
        $this->WriteAttributeInteger('registrationId', $registrationId);
    }

    public function GetAccessToken()
    {
        $token = $this->ReadAttributeString('AccessToken');
        return $token;
    }

    /** Get current state
     *
     * @return array|mixed
     */
    public function GetCurrentState()
    {
        $result    = $this->SendCommand(self::APIPATH, 'GET');
        $data_json = 'could not get data';
        if ($result['http_code'] == 200) {
            $data_json = $result['body'];

            $data        = json_decode($data_json);
            $device_info = $data->device;
            $this->SendDebug('Device Info', json_encode($device_info), 0);

            $name = $device_info->name;
            $this->WriteAttributeString('name', $name); // Friendly name of the device
            $deviceType = $device_info->deviceType;
            $this->WriteAttributeString('deviceType', $deviceType); // Device Type identifier – currently fixed to HSB1
            $uniqueId = $device_info->uniqueId;
            $this->WriteAttributeString('uniqueId', $uniqueId); // Capitalized hex string of the 6 byte / 12 characters device id without delimiters. Used as unique id on label, certificate common name, hostname etc.
            $ip = $device_info->ipAddress;
            $this->WriteAttributeString('ipAddress', $ip); // Local IP address of the device
            $apiLevel = $device_info->apiLevel;
            $this->WriteAttributeInteger('apiLevel', $apiLevel); // Increased between firmware versions when api changes. Only apiLevel >= 4 is supported.
            $firmwareVersion = $device_info->firmwareVersion;
            $this->WriteAttributeString('firmwareVersion', $firmwareVersion); // User readable version of the device firmware, starting with decimal major .minor .maintenance format e.g. “1.12.3”
            $this->SetValue('firmwareVersion', $firmwareVersion);
            $buildNumber = $device_info->buildNumber;
            $this->WriteAttributeInteger('buildNumber', $buildNumber); // Build number of the firmware. Unique for every build with newer builds guaranteed a higher number than older.
            $lastCheckedUpdate = $device_info->lastCheckedUpdate;
            $this->WriteAttributeString('lastCheckedUpdate', $lastCheckedUpdate); // UTC time when last check for update was performed.
            $updatableBuildNumber = $device_info->updatableBuildNumber;
            $this->WriteAttributeString('updatableBuildNumber', $updatableBuildNumber); // Build number that is available to update to. Item is set to null when there is no update available.
            $updatableFirmwareVersion = $device_info->updatableFirmwareVersion;
            $this->WriteAttributeString('updatableFirmwareVersion', $updatableFirmwareVersion); // User readable version of the firmware the device can upgrade to. Item is set to null when there is no update available.
            $autoUpdateEnabled = $device_info->update->autoUpdateEnabled; // update Root object for automatic update configuration
            $this->WriteAttributeBoolean('autoUpdateEnabled', $autoUpdateEnabled); // Sync Box checks daily for a firmware update. If true, an available update will automatically be installed. This will be postponed if Sync Box is passing through content to the TV and being used.
            $autoUpdateTime = $device_info->update->autoUpdateTime; // update Root object for automatic update configuration
            $this->WriteAttributeInteger('autoUpdateTime', $autoUpdateTime); // UTC hour when the automatic update will check and execute, values 0 – 23. Default is 10. Ideally this value should be set to 3AM according to user’s timezone.
            $ledMode = $device_info->ledMode;
            $this->WriteAttributeInteger('ledMode', $ledMode); // 1 = regular; 0 = off in powersave, passthrough or sync mode; 2 = dimmed in powersave or passthrough mode and off in sync mode
            $this->SetValue('ledMode', $ledMode);
            $wifiState = $device_info->wifiState;
            $this->WriteAttributeString('wifiState', $wifiState); // uninitialized, disconnected, lan, wan
            $termsAgreed = $device_info->lastCheckedUpdate;
            $this->WriteAttributeBoolean('termsAgreed', $termsAgreed);
            $device_action = $device_info->action;
            $this->WriteAttributeString('device_action', $device_action); // none, doSoftwareRestart,  doFirmwareUpdate
            $capabilities = $device_info->capabilities; // capabilities Root object for capabilities resource
            $maxIrCodes = $capabilities->maxIrCodes; // The total number of IR codes configurable
            $this->WriteAttributeInteger('maxIrCodes', $maxIrCodes); // The total number of IR codes configurable
            $maxPresets = $capabilities->maxPresets; // The total number of Presets configurable
            $this->WriteAttributeInteger('maxPresets', $maxPresets); // The total number of Presets configurable


            $hue = $data->hue; // Root object for hue resource
            $this->SendDebug('Hue Info', json_encode($hue), 0);
            $bridgeUniqueId = $hue->bridgeUniqueId; // 16 character ascii hex string bridge identifier
            $this->WriteAttributeString('hue_bridgeUniqueId', $bridgeUniqueId);
            $bridgeIpAddress = $hue->bridgeIpAddress; // Readable, dot IPv4 address of the paired bridge EG “192.168.1.50”
            $this->WriteAttributeString('hue_bridgeIpAddress', $bridgeIpAddress);
            $groupId = $hue->groupId;
            $this->WriteAttributeString('hue_groupId', $groupId);
            $groups = $hue->groups; // All available entertainment areas on the current bridge. When this object is not available, it means the bridge groups have not been retrieved yet. When the object is empty, it means there are no entertainment areas on the bridge. When the bridge connection is lost, the last known values are remembered. Determining whether values may be outdated can be done based on connectionState.
            $this->WriteAttributeString('hue_groups', json_encode($groups));
            $connectionState = $hue->connectionState;
            $this->WriteAttributeString('hue_connectionState', $connectionState);

            $execution = $data->execution; // Root object for execution resource
            $this->SendDebug('Execution Info', json_encode($execution), 0);
            $mode = $execution->mode; // powersave, passthrough, video, game, music, ambient (More modes can be added in the future, so clients must gracefully handle modes they don’t recognize)
            $this->SetValue('Mode', $this->GetModeValue($mode));
            $this->WriteAttributeInteger('Mode', $this->GetModeValue($mode));
            if ($mode == 'passthrough' || $mode == 'video' || $mode == 'music' || $mode == 'game') {
                $this->SetValue('State', true);
            } elseif ($mode == 'powersave') {
                $this->SetValue('State', false);
            }
            $syncActive = $execution->syncActive; // Reports false in case of powersave or passthrough mode, and true in case of video, game, music, or ambient mode. When changed from false to true, it will start syncing in last used mode for current source. Requires hue /connectionState to be connected. When changed from true to false, will set passthrough mode.
            $this->WriteAttributeBoolean('syncActive', $syncActive);
            if ($this->GetIDForIdent('syncActive') > 0) {
                $this->SetValue('syncActive', $syncActive);
            }
            $hdmiActive = $execution->hdmiActive; // Reports false in case of powersave mode, and true in case of passthrough, video, game, music or ambient mode. When changed from false to true, it will set passthrough mode. When changed from true to false, will set powersave mode.
            $this->WriteAttributeBoolean('hdmiActive', $hdmiActive);
            if ($this->GetIDForIdent('hdmiActive') > 0) {
                $this->SetValue('hdmiActive', $hdmiActive);
            }
            $hdmiSource = $execution->hdmiSource; // input1, input2, input3, input4 (currently selected hdmi input)
            $this->WriteAttributeInteger('Input', $this->GetHDMIValue($hdmiSource));
            $this->SetValue('Input', $this->GetHDMIValue($hdmiSource));
            $hueTarget = $execution->hueTarget;  // groups/<groupId> (currently selected entertainment area)
            $this->WriteAttributeString('hueTarget', $hueTarget);

            $brightness = $execution->brightness; // // 0 – 200 (100 = no brightness reduction/boost compared to input, 0 = max reduction, 200 = max boost)
            $this->WriteAttributeInteger('Brightness', $brightness);
            $this->SetValue('Brightness', $brightness);
            $lastSyncMode = $execution->lastSyncMode; // video, game, music, ambient
            $this->WriteAttributeString('lastSyncMode', $lastSyncMode);
            $video = $execution->video; // Root for video subresource
            $this->WriteAttributeString('video', json_encode($video));
            $video_intensity = $video->intensity;
            $this->WriteAttributeString('video_intensity', $video_intensity); // subtle, moderate, high, intense
            $backlight_video = $video->backgroundLighting;
            $this->WriteAttributeBoolean('backlight_video', $backlight_video);
            $game = $execution->game; // Root for game subresource
            $this->WriteAttributeString('game', json_encode($game));
            $game_intensity = $game->intensity;
            $this->WriteAttributeString('game_intensity', $game_intensity); // subtle, moderate, high, intense
            $backlight_game = $game->backgroundLighting;
            $this->WriteAttributeBoolean('backlight_game', $backlight_game);
            $music = $execution->music; // Root for music subresource
            $this->WriteAttributeString('music', json_encode($music));
            $music_intensity = $music->intensity;
            $this->WriteAttributeString('music_intensity', $music_intensity); // subtle, moderate, high, intense
            $music_palette = $music->palette; // happyEnergetic, happyCalm, melancholicCalm, melancholic Energetic, neutral
            $this->WriteAttributeString('music_palette', $music_palette);
            $this->SetValue('music_palette', $this->GetPaletteValue($music_palette));
            if(isset($execution->preset))
            {
                $preset = $execution->preset; // Preset identifier, that will be executed
                $this->WriteAttributeString('preset', $preset);
            }
            if(isset($execution->ambient))
            {
                $ambient = $execution->ambient;
                $this->WriteAttributeString('ambient', json_encode($ambient));
            }

            if ($lastSyncMode == 'video') {
                $intensity = $video->intensity;
                $this->SetValue('Intensity', $this->GetIntensityValue($intensity));
            }
            if ($lastSyncMode == 'game') {
                $intensity = $game->intensity;
                $this->SetValue('Intensity', $this->GetIntensityValue($intensity));
            }
            if ($lastSyncMode == 'music') {
                $intensity = $music->intensity;
                $this->SetValue('Intensity', $this->GetIntensityValue($intensity));
            }

            $hdmi = $data->hdmi; // Root object for hdmi resource
            $this->SendDebug('HDMI Info', json_encode($hdmi), 0);

            $input1      = $hdmi->input1;
            $input1_name = $input1->name; // Friendly name, not empty
            $this->WriteAttributeString('input1_name', $input1_name);
            if (@$this->GetIDForIdent('input1_name') > 0) {
                $this->SetValue('input1_name', $input1_name);
            }
            $input1_type = $input1->type; // Friendly type: generic, video, game, music, xbox, playstation, nintendoswitch, phone, desktop, laptop, appletv, roku, shield, chromecast, firetv, diskplayer, settopbox, satellite, avreceiver, soundbar, hdmiswitch
            $this->WriteAttributeString('input1_type', $input1_type);
            $input1_status = $input1->status;
            $this->WriteAttributeString('input1_status', $input1_status);
            $input1_lastSyncMode = $input1->lastSyncMode;
            $this->WriteAttributeString('input1_lastSyncMode', $input1_lastSyncMode);
            $input2      = $hdmi->input2;
            $input2_name = $input2->name; // Friendly name, not empty
            $this->WriteAttributeString('input2_name', $input2_name);
            if (@$this->GetIDForIdent('input2_name') > 0) {
                $this->SetValue('input2_name', $input2_name);
            }
            $input2_type = $input2->type; // Friendly type: generic, video, game, music, xbox, playstation, nintendoswitch, phone, desktop, laptop, appletv, roku, shield, chromecast, firetv, diskplayer, settopbox, satellite, avreceiver, soundbar, hdmiswitch
            $this->WriteAttributeString('input2_type', $input2_type);
            $input2_status = $input2->status;
            $this->WriteAttributeString('input2_status', $input2_status);
            $input2_lastSyncMode = $input2->lastSyncMode;
            $this->WriteAttributeString('input2_lastSyncMode', $input2_lastSyncMode);
            $input3      = $hdmi->input3;
            $input3_name = $input3->name; // Friendly name, not empty
            $this->WriteAttributeString('input3_name', $input3_name);
            if (@$this->GetIDForIdent('input3_name') > 0) {
                $this->SetValue('input3_name', $input3_name);
            }
            $input3_type = $input3->type; // Friendly type: generic, video, game, music, xbox, playstation, nintendoswitch, phone, desktop, laptop, appletv, roku, shield, chromecast, firetv, diskplayer, settopbox, satellite, avreceiver, soundbar, hdmiswitch
            $this->WriteAttributeString('input3_type', $input3_type);
            $input3_status = $input3->status;
            $this->WriteAttributeString('input3_status', $input3_status);
            $input3_lastSyncMode = $input3->lastSyncMode;
            $this->WriteAttributeString('input3_lastSyncMode', $input3_lastSyncMode);
            $input4      = $hdmi->input4;
            $input4_name = $input4->name; // Friendly name, not empty
            $this->WriteAttributeString('input4_name', $input4_name);
            if (@$this->GetIDForIdent('input4_name') > 0) {
                $this->SetValue('input4_name', $input4_name);
            }
            $input4_type = $input4->type; // Friendly type: generic, video, game, music, xbox, playstation, nintendoswitch, phone, desktop, laptop, appletv, roku, shield, chromecast, firetv, diskplayer, settopbox, satellite, avreceiver, soundbar, hdmiswitch
            $this->WriteAttributeString('input4_type', $input4_type);
            $input4_status = $input4->status;
            $this->WriteAttributeString('input4_status', $input4_status);
            $input4_lastSyncMode = $input4->lastSyncMode;
            $this->WriteAttributeString('input4_lastSyncMode', $input4_lastSyncMode);
            $output      = $hdmi->output;
            $output_name = $output->name;
            $this->WriteAttributeString('output_name', $output_name);
            $output_type = $output->type;
            $this->WriteAttributeString('output_type', $output_type);
            $output_status = $output->status;
            $this->WriteAttributeString('output_status', $output_status);
            $output_lastSyncMode = $output->lastSyncMode;
            $this->WriteAttributeString('output_lastSyncMode', $output_lastSyncMode);
            $contentSpecs = $hdmi->contentSpecs; // <horizontal pixels> x <vertical pixels> @ <framerate fpks> – <HDR>
            $this->WriteAttributeString('contentSpecs', $contentSpecs);
            $videoSyncSupported = $hdmi->videoSyncSupported; // Current content specs supported for video sync (video/game mode)
            $this->WriteAttributeBoolean('videoSyncSupported', $videoSyncSupported);
            $audioSyncSupported = $hdmi->audioSyncSupported; // Current content specs supported for audio sync (music mode)
            $this->WriteAttributeBoolean('audioSyncSupported', $audioSyncSupported);

            $behavior = $data->behavior; // Root object for behavior resource
            $this->SendDebug('Device Behavior', json_encode($behavior), 0);
            $inactivePowersave = $behavior->inactivePowersave; // Device automatically goes to powersave after this many minutes of being in passthrough mode with no link on any source or no link on output. 0 is disabled, max is 10000. Default: 20.
            $this->WriteAttributeInteger('inactivePowersave', $inactivePowersave);
            $cecPowersave = $behavior->cecPowersave; // Device goes to powersave when TV sends CEC OFF. Default: 1. Disabled 0, Enabled 1.
            $this->WriteAttributeInteger('cecPowersave', $cecPowersave);
            if (@$this->GetIDForIdent('cecPowersave') > 0) {
                $this->SetValue('cecPowersave', boolval($cecPowersave));
            }
            $usbPowersave = $behavior->usbPowersave; // Device goes to powersave when USB power transitions from 5V to 0V. Default: 1. Disabled 0, Enabled 1.
            $this->WriteAttributeInteger('usbPowersave', $usbPowersave);
            if (@$this->GetIDForIdent('usbPowersave') > 0) {
                $this->SetValue('usbPowersave', boolval($usbPowersave));
            }
            $hpdInputSwitch = $behavior->hpdInputSwitch;
            $this->WriteAttributeInteger('hpdInputSwitch', $hpdInputSwitch);


            $arcBypassMode = $behavior->arcBypassMode;
            $this->WriteAttributeInteger('arcBypassMode', $arcBypassMode);
            if (@$this->GetIDForIdent('arcBypassMode') > 0) {
                $this->SetValue('arcBypassMode', boolval($arcBypassMode));
            }
            $forceDoviNative = $behavior->forceDoviNative; // When the TV advertises Dolby Vision force to use native native mode. Disabled 0, Enabled 1.
            $this->WriteAttributeInteger('forceDoviNative', $forceDoviNative);
            if (@$this->GetIDForIdent('forceDoviNative') > 0) {
                $this->SetValue('forceDoviNative', boolval($forceDoviNative));
            }
            $input1_cecInputSwitch = $behavior->input1->cecInputSwitch;
            $this->WriteAttributeInteger('input1_cecInputSwitch', $input1_cecInputSwitch);
            $input1_linkAutoSync = $behavior->input1->linkAutoSync;
            $this->WriteAttributeInteger('input1_linkAutoSync', $input1_linkAutoSync);
            $input2_cecInputSwitch = $behavior->input2->cecInputSwitch;
            $this->WriteAttributeInteger('input2_cecInputSwitch', $input2_cecInputSwitch);
            $input2_linkAutoSync = $behavior->input2->linkAutoSync;
            $this->WriteAttributeInteger('input2_linkAutoSync', $input2_linkAutoSync);
            $input3_cecInputSwitch = $behavior->input3->cecInputSwitch;
            $this->WriteAttributeInteger('input3_cecInputSwitch', $input3_cecInputSwitch);
            $input3_linkAutoSync = $behavior->input3->linkAutoSync;
            $this->WriteAttributeInteger('input3_linkAutoSync', $input3_linkAutoSync);
            $input4_cecInputSwitch = $behavior->input4->cecInputSwitch;
            $this->WriteAttributeInteger('input4_cecInputSwitch', $input4_cecInputSwitch);
            $input4_linkAutoSync = $behavior->input4->linkAutoSync;
            $this->WriteAttributeInteger('input4_linkAutoSync', $input4_linkAutoSync);

            $ir = $data->ir; // Root object for IR resource
            $scan = $ir->scan;
            $scanning = $scan->scanning; // Scanning mode causes the last-received IR code to be saved (instead of processing), displayed as the ‘code’ attribute. Scanning automatically deactivates after 20 seconds but can continually be enabled again without gaps. After scanning an IR code, scanning will immediately be disabled.
            $this->WriteAttributeBoolean('scanning', $scanning);
            $code = $scan->code; // The last scanned code received while in scanning mode. Value is null if not scanned.
            $this->WriteAttributeString('code', $code);
            $this->SendDebug('Last IR Code', $code, 0);
            $codes = $ir->codes; // The last scanned code received while in scanning mode. Value is null if not scanned.
            $this->WriteAttributeString('codes', json_encode($codes));
            $this->SendDebug('IR Codes', json_encode($codes), 0);

            $registrations = $data->registrations;
            $this->SendDebug('Registrations', json_encode($registrations), 0);
            $this->WriteAttributeString('registrations', json_encode($registrations));
            $presets = $data->presets;
            $this->SendDebug('Presets', json_encode($presets), 0);
            $this->WriteAttributeString('presets', json_encode($presets));
        }
        return $data_json;
    }

    private function GetModeValue($mode)
    {
        // powersave, passthrough, video, game, music, ambient (More modes can be added in the future, so clients must gracefully handle modes they don’t recognize)
        if ($mode == 'passthrough') {
            $mode_value = 0;
        } elseif ($mode == 'powersave') {
            $mode_value = 1;
        } elseif ($mode == 'video') {
            $mode_value = 2;
        } elseif ($mode == 'music') {
            $mode_value = 3;
        } elseif ($mode == 'game') {
            $mode_value = 4;
        } else {
            $mode_value = 1;
        }
        return $mode_value;
    }

    private function GetHDMIValue($hdmiSource)
    {
        // input1, input2, input3, input4 (currently selected hdmi input)
        if ($hdmiSource == 'input1') {
            $hdmiSource_value = 0;
        } elseif ($hdmiSource == 'input2') {
            $hdmiSource_value = 1;
        } elseif ($hdmiSource == 'input3') {
            $hdmiSource_value = 2;
        } elseif ($hdmiSource == 'input4') {
            $hdmiSource_value = 3;
        } else {
            $hdmiSource_value = 1;
        }
        return $hdmiSource_value;
    }

    private function GetIntensityValue($intensity)
    {
        if ($intensity == 'subtle') {
            $intensity_value = 0;
        } elseif ($intensity == 'moderate') {
            $intensity_value = 1;
        } elseif ($intensity == 'high') {
            $intensity_value = 2;
        } elseif ($intensity == 'intense') {
            $intensity_value = 3;
        } else {
            $intensity_value = 1;
        }
        return $intensity_value;
    }

    private function GetPaletteValue($palette)
    {
        if ($palette == 'happyEnergetic') {
            $palette_value = 0;
        } elseif ($palette == 'happyCalm') {
            $palette_value = 1;
        } elseif ($palette == 'melancholicCalm') {
            $palette_value = 2;
        } elseif ($palette == 'melancholic Energetic') {
            $palette_value = 3;
        } elseif ($palette == 'neutral') {
            $palette_value = 4;
        }else {
            $palette_value = 1;
        }
        return $palette_value;
    }

    /** Power on
     *
     * @return array
     */
    public function PowerOn()
    {
        $this->SetValue('State', true);
        $response = $this->Mode('passthrough');
        $this->GetCurrentState();
        return $response;
    }

    /** Power off
     *
     * @return array
     */
    public function PowerOff()
    {
        $this->SetValue('State', false);
        $response = $this->Mode('powersave');
        $this->GetCurrentState();
        return $response;
    }

    /** Power Toggle
     *
     */
    public function PowerToggle()
    {
        $state = GetValue($this->GetIDForIdent('State'));
        if ($state) {
            $this->PowerOff();
        } else {
            $this->PowerOn();
        }
    }

    /** LED Mode
     * 1 = regular; 0 = off in powersave, passthrough or sync mode; 2 = dimmed in powersave or passthrough mode and off in sync mode
     * @param int $mode
     *
     * @return mixed
     */
    public function LEDMode(int $mode)
    {
        return $this->SendDevice(['ledMode' => $mode]);
    }

    /**
     * true toggles syncActive
     */
    public function toggleSyncActive()
    {
        $response = $this->SendExecution(['toggleSyncActive' => true]);
        $this->GetCurrentState();
        return $response;
    }

    /** Sync Active
     * Reports false in case of powersave or passthrough mode, and true in case of video, game, music, or ambient mode. When changed from false to true, it will start syncing in last used mode for current source. Requires hue /connectionState to be connected. When changed from true to false, will set passthrough mode.
     * @param bool $state
     *
     * @return mixed
     */
    public function SyncActive(bool $state)
    {
        $response = $this->SendExecution(['syncActive' => $state]);
        $this->GetCurrentState();
        return $response;
    }

    /**
     * true toggles hdmiActive
     */
    public function toggleHdmiActive()
    {
        $response = $this->SendExecution(['toggleHdmiActive' => true]);
        $this->GetCurrentState();
        return $response;
    }

    /** HDMI Active
     * Reports false in case of powersave mode, and true in case of passthrough, video, game, music or ambient mode. When changed from false to true, it will set passthrough mode. When changed from true to false, will set powersave mode.
     * @param bool $state
     *
     * @return mixed
     */
    public function HDMIActive(bool $state)
    {
        $response = $this->SendExecution(['hdmiActive' => $state]);
        $this->GetCurrentState();
        return $response;
    }

    /** Previous SyncMode
     *
     */
    public function PreviousSyncMode()
    {
        $response = $this->SendExecution(['cycleSyncMode' => 'previous']);
        $this->GetCurrentState();
        return $response;
    }

    /** Next SyncMode
     *
     */
    public function NextSyncMode()
    {
        $response = $this->SendExecution(['cycleSyncMode' => 'next']);
        $this->GetCurrentState();
        return $response;
    }

    /** Previous HDMI Source
     *
     */
    public function PreviousHDMISource()
    {
        $response = $this->SendExecution(['cycleHdmiSource' => 'previous']);
        $this->GetCurrentState();
        return $response;
    }

    /** Next HDMI Source
     *
     */
    public function NextHDMISource()
    {
        $response = $this->SendExecution(['cycleHdmiSource' => 'next']);
        $this->GetCurrentState();
        return $response;
    }

    /** Previous Intensity
     * cycle intensity of current mode if syncing
     */
    public function PreviousIntensity()
    {
        $response = $this->SendExecution(['cycleIntensity' => 'previous']);
        $this->GetCurrentState();
        return $response;
    }

    /** Next Intensity
     * cycle intensity of current mode if syncing
     */
    public function NextIntensity()
    {
        $response = $this->SendExecution(['cycleIntensity' => 'next']);
        $this->GetCurrentState();
        return $response;
    }

    /** Change mode
     *
     * @param string $mode passthrough | powersave | video | music | game
     * powersave, passthrough, video, game, music, ambient (More modes can be added in the future, so clients must gracefully handle modes they don’t recognize)
     * @return array
     */
    public function Mode(string $mode)
    {
        if ($mode == 'video') {
            $video     = json_decode($this->ReadAttributeString('video'));
            $intensity = $video->intensity;
            $this->SetValue('Intensity', $this->GetIntensityValue($intensity));
        }
        if ($mode == 'music') {
            $music     = json_decode($this->ReadAttributeString('music'));
            $intensity = $music->intensity;
            $this->SetValue('Intensity', $this->GetIntensityValue($intensity));
        }
        if ($mode == 'game') {
            $game      = json_decode($this->ReadAttributeString('game'));
            $intensity = $game->intensity;
            $this->SetValue('Intensity', $this->GetIntensityValue($intensity));
        }
        $response = $this->SendExecution(['mode' => $mode]);
        $this->GetCurrentState();
        return $response;
    }

    /** Change brightness
     *
     * @param int $brightness 0 - 200
     *
     * @return array
     */
    public function Brightness(int $brightness)
    {
        $response = $this->SendExecution(['brightness' => $brightness]);
        $this->GetCurrentState();
        return $response;
    }

    /** Change intensity
     *
     * @param string $mode      video | music | game
     * @param string $intensity subtle | moderate | high | intense
     *
     * @return mixed
     */
    public function Intensity(string $mode, string $intensity)
    {
        $response = $this->SendExecution([$mode => ['intensity' => $intensity]]);
        $this->GetCurrentState();
        return $response;
    }

    /** Palette
     *
     * @param string $palette happyEnergetic, happyCalm, melancholicCalm, melancholic Energetic, neutral
     *
     * @return mixed
     */
    public function Palette(string $palette)
    {
        $response = $this->SendExecution(['music' => ['palette' => $palette]]);
        $this->GetCurrentState();
        return $response;
    }

    /** Set HDMI Input
     *
     * @param int $input
     *
     * @return mixed
     */
    public function SetHDMIInput(int $input)
    {
        $response = $this->SendExecution(['hdmiSource' => 'input' . $input]);
        $this->GetCurrentState();
        return $response;
    }

    /** Check firmware update available
     * No response, need to query device state
     */
    public function CheckFirmwareUpdate()
    {
        $this->SendDevice(['action' => 'checkForFirmwareUpdates']);
    }

    /** Set firmware autoupdate
     * Sync Box checks daily for a firmware update. If true, an available update will automatically be installed. This will be postponed if Sync Box is passing through content to the TV and being used.
     * autoUpdateTime UTC hour when the automatic update will check and execute, values 0 – 23. Default is 10. Ideally this value should be set to 3AM according to user’s timezone.
     * @return mixed
     */
    public function SetFirmwareAutoupdate()
    {
        return $this->SendDevice(['update' => ['autoUpdateEnabled' => true, 'autoUpdateTime' => 2]]);
    }

    /** Register Hue Bridge
     *
     * @param string $bridge_ip
     * @param string $bridgeUniqueId
     * @param string $clientKey
     * @param string $username
     *
     * @return mixed
     */
    public function RegisterHueBridge(string $bridge_ip, string $bridgeUniqueId, string $clientKey, string $username)
    {
        return $this->SendHue(
            ['bridgeIpAddress' => $bridge_ip, 'bridgeUniqueId' => $bridgeUniqueId, 'clientKey' => $clientKey, 'username' => $username]
        );
    }

    /** Define controlled entertainment area
     *
     * @param string $area_id entertainment area id
     *
     * @return mixed
     */
    public function DefineControlledEntertainmentArea(string $area_id)
    {
        $entertainment_area = $this->SendHue(['groupId' => $area_id]);
        return $entertainment_area;
    }

    public function DefineInput1Name(string $name)
    {
        $name2       = $this->ReadAttributeString('input2_name');
        $name3       = $this->ReadAttributeString('input3_name');
        $name4       = $this->ReadAttributeString('input4_name');
        $input_names = $this->DefineInputNames($name, $name2, $name3, $name4);
        return $input_names;
    }

    public function DefineInput2Name(string $name)
    {
        $name1       = $this->ReadAttributeString('input1_name');
        $name3       = $this->ReadAttributeString('input3_name');
        $name4       = $this->ReadAttributeString('input4_name');
        $input_names = $this->DefineInputNames($name1, $name, $name3, $name4);
        return $input_names;
    }

    public function DefineInput3Name(string $name)
    {
        $name1       = $this->ReadAttributeString('input1_name');
        $name2       = $this->ReadAttributeString('input2_name');
        $name4       = $this->ReadAttributeString('input4_name');
        $input_names = $this->DefineInputNames($name1, $name2, $name, $name4);
        return $input_names;
    }

    public function DefineInput4Name(string $name)
    {
        $name1       = $this->ReadAttributeString('input1_name');
        $name2       = $this->ReadAttributeString('input2_name');
        $name3       = $this->ReadAttributeString('input3_name');
        $input_names = $this->DefineInputNames($name1, $name2, $name3, $name);
        return $input_names;
    }

    /** Define input names
     *
     * @param string $name1
     * @param string $name2
     * @param string $name3
     * @param string $name4
     *
     * @return array|mixed
     */
    protected function DefineInputNames(string $name1, string $name2, string $name3, string $name4)
    {
        $input_names = $this->SendCommand(
            self::APIPATH . 'hdmi', 'PUT',
            ['input1' => ['name' => $name1], 'input2' => ['name' => $name2], 'input3' => ['name' => $name3], 'input4' => ['name' => $name4]]
        );
        $input_names = json_decode($input_names['body']);
        return $input_names;
    }

    /** CEC power state detection
     * Device goes to powersave when TV sends CEC OFF. Default: 1. Disabled 0, Enabled 1.
     * @param bool $state
     *
     * @return mixed
     */
    public function CEC_PowerStateDetection(bool $state)
    {
        $response = $this->SendBehavior(['cecPowersave' => intval($state)]);
        $this->GetCurrentState();
        return $response;
    }

    /** USB power state detection
     * Device goes to powersave when USB power transitions from 5V to 0V. Default: 1. Disabled 0, Enabled 1.
     * @param bool $state
     *
     * @return mixed
     */
    public function USB_PowerStateDetection(bool $state)
    {
        $response = $this->SendBehavior(['usbPowersave' => intval($state)]);
        $this->GetCurrentState();
        return $response;
    }

    /** HDMI inactivity power state
     * Device automatically goes to powersave after this many minutes of being in passthrough mode with no link on any source or no link on output. 0 is disabled, max is 10000. Default: 20.
     * @param int $minutes
     *
     * @return mixed
     */
    public function HDMI_InactivityPowerState(int $minutes)
    {
        $response = $this->SendBehavior(['inactivePowersave' => $minutes]);
        $this->GetCurrentState();
        return $response;
    }

    /** HDMI input detected
     * Automatically switch input when any source is plugged in (or powered on). Default: 1. Disabled 0, Enabled 1.
     * @param bool $state
     *
     * @return mixed
     */
    public function HDMI_InputDetected(bool $state)
    {
        $response = $this->SendBehavior(['hpdInputSwitch' => intval($state)]);
        $this->GetCurrentState();
        return $response;
    }

    /** Auto switch inputs
     * Automatically switch input when this source sends CEC active. Default: 1. Disabled 0, Enabled 1.
     * @param int  $input
     * @param bool $state
     *
     * @return mixed
     */
    public function AutoSwitchInputs(int $input, bool $state)
    {
        $response = $this->SendBehavior(['input' . $input => ['cecInputSwitch' => intval($state)]]);
        $this->GetCurrentState();
        return $response;
    }

    /** Auto Sync
     *
     * @param int  $input
     * @param bool $state
     *
     * @return mixed
     */
    public function AutoSync(int $input, bool $state)
    {
        $response = $this->SendBehavior(['input' . $input => ['linkAutoSync' => intval($state)]]);
        $this->GetCurrentState();
        return $response;
    }

    /** Toogle background lighting
     *
     * @param string $mode video | game
     * @param bool   $state
     *
     * @return mixed
     */
    public function BackgroundLighting(string $mode, bool $state)
    {
        $response = $this->SendExecution([$mode => ['backgroundLighting' => $state]]);
        $this->GetCurrentState();
        return $response;
    }

    /** ARC Bypass
     *
     * @return mixed
     */
    public function ARC_Bypass(bool $state)
    {
        $response = $this->SendBehavior(['arcBypassMode' => intval($state)]);
        $this->GetCurrentState();
        return $response;
    }

    /** Force Dolby Vision
     * When the TV advertises Dolby Vision force to use native native mode. Disabled 0, Enabled 1.
     * @return mixed
     */
    public function ForceDolbyVision(bool $state)
    {
        $response = $this->SendBehavior(['forceDoviNative' => intval($state)]);
        $this->GetCurrentState();
        return $response;
    }

    /** Scanning mode IR
     * Scanning mode causes the last-received IR code to be saved (instead of processing), displayed as the ‘code’ attribute. Scanning automatically deactivates after 20 seconds but can continually be enabled again without gaps. After scanning an IR code, scanning will immediately be disabled.
     * @param bool $state
     *
     * @return mixed
     */
    public function IRScan(bool $state)
    {
        $response = $this->SendIR(['scan' => ['scanning' => intval($state)]]);
        $this->GetCurrentState();
        return $response;
    }

    /** Restart sync box
     *
     * @return mixed
     */
    public function RestartSyncBox()
    {
        $postfields = ['action' => 'doSoftwareRestart'];
        $data       = $this->SendCommand(self::APIPATH . '/device', 'PUT', $postfields);
        $result     = json_decode($data['body']);
        return $result;
    }

    private function SendExecution($postfields)
    {
        $data = $this->SendCommand(self::APIPATH . '/execution', 'PUT', $postfields);
        return json_decode($data['body']);
    }

    private function SendBehavior($postfields)
    {
        $data = $this->SendCommand(self::APIPATH . '/behavior', 'PUT', $postfields);
        return json_decode($data['body']);
    }

    private function SendIR($postfields)
    {
        $data = $this->SendCommand(self::APIPATH . '/ir', 'PUT', $postfields);
        return json_decode($data['body']);
    }

    private function SendDevice($postfields = null)
    {
        if ($postfields === null) {
            $data = $this->SendCommand(self::APIPATH . '/device', 'GET', $postfields);
        } else {
            $data = $this->SendCommand(self::APIPATH . '/device', 'PUT', $postfields);
        }
        return json_decode($data['body']);
    }

    private function SendHue($postfields)
    {
        $data = $this->SendCommand(self::APIPATH . '/hue', 'PUT', $postfields);
        return json_decode($data['body']);
    }

    public function SetVoiceControl(string $type)
    {
        if($type == 'Alexa')
        {
            $this->SendDebug('Hue Sync', 'Setup Alexa Voice Control', 0);
        }
        if($type == 'Google')
        {
            $this->SendDebug('Hue Sync', 'Setup Google Voice Control', 0);
        }
        if($type == 'Homekit')
        {
            $this->SendDebug('Hue Sync', 'Setup Homekit Voice Control', 0);
        }
    }

    public function SetupHueSyncScripts()
    {
        $huesyncscript = $this->ReadPropertyBoolean('HueSyncScript');
        $cat_id = $this->ReadPropertyInteger('ImportCategoryID');
        if($huesyncscript && $cat_id > 0)
        {
            $HueSyncScriptCategoryID = $this->CreateHueSyncScriptCategory();
            $this->CreateHueSyncScripts($HueSyncScriptCategoryID);
        }
    }

    protected function CreateHueSyncScriptCategory()
    {
        $CategoryID = $this->ReadPropertyInteger('ImportCategoryID');
        //Prüfen ob Kategorie schon existiert
        $HueSyncScriptCategoryID = @IPS_GetObjectIDByIdent('CatHueSyncScripts', $CategoryID);
        if ($HueSyncScriptCategoryID === false) {
            $HueSyncScriptCategoryID = IPS_CreateCategory();
            IPS_SetName($HueSyncScriptCategoryID, $this->Translate('Hue Sync Scripts'));
            IPS_SetIdent($HueSyncScriptCategoryID, 'CatHueSyncScripts');
            IPS_SetInfo($HueSyncScriptCategoryID, $this->Translate('Hue Sync Scripts'));
            IPS_SetParent($HueSyncScriptCategoryID, $CategoryID);
        }
        $this->SendDebug('Hue Sync Script Category', strval($HueSyncScriptCategoryID), 0);

        return $HueSyncScriptCategoryID;
    }

    protected function CreateHueSyncScripts($HueSyncScriptCategoryID)
    {
        $content = '<?php HUESYNC_SetHDMIInput(' . $this->InstanceID . ', 1);';
        $this->CreateScript('Hue Sync Box HDMI 1', $this->CreateIdent('Hue Sync Box HDMI 1'), $HueSyncScriptCategoryID, $content);
        $content = '<?php HUESYNC_SetHDMIInput(' . $this->InstanceID . ', 2);';
        $this->CreateScript('Hue Sync Box HDMI 2', $this->CreateIdent('Hue Sync Box HDMI 2'), $HueSyncScriptCategoryID, $content);
        $content = '<?php HUESYNC_SetHDMIInput(' . $this->InstanceID . ', 3);';
        $this->CreateScript('Hue Sync Box HDMI 3', $this->CreateIdent('Hue Sync Box HDMI 3'), $HueSyncScriptCategoryID, $content);
        $content = '<?php HUESYNC_SetHDMIInput(' . $this->InstanceID . ', 4);';
        $this->CreateScript('Hue Sync Box HDMI 4', $this->CreateIdent('Hue Sync Box HDMI 4'), $HueSyncScriptCategoryID, $content);
        $content = '<?php HUESYNC_SetHDMIInput(' . $this->InstanceID . ', 4);';
        $this->CreateScript('Hue Sync Box HDMI 4', $this->CreateIdent('Hue Sync Box HDMI 4'), $HueSyncScriptCategoryID, $content);
        $content = '<?php HUESYNC_Mode(' . $this->InstanceID . ', \'passthrough\');';
        $ScriptID_PowerOn = $this->CreateScript('Hue Sync Box Power On', $this->CreateIdent('Hue Sync Box Power On'), $HueSyncScriptCategoryID, $content);
        $content = '<?php HUESYNC_Mode(' . $this->InstanceID . ', \'powersave\');';
        $ScriptID_PowerOff = $this->CreateScript('Hue Sync Box Power Off', $this->CreateIdent('Hue Sync Box Power Off'), $HueSyncScriptCategoryID, $content);
        $content = '<?php
$status = GetValueBoolean(IPS_GetObjectIDByIdent(\'State\', '. $this->InstanceID.')); // Status des Geräts auslesen
IPS_LogMessage( "Hue Sync Box:" , "Befehl ausführen" );
if ($status == false)// Befehl ausführen
	{
   IPS_RunScript('.$ScriptID_PowerOn.');
	}
elseif ($status == true)// Befehl ausführen
	{
   IPS_RunScript('.$ScriptID_PowerOff.');
	}';
        $this->CreateScript('Hue Sync Box Power Toggle', $this->CreateIdent('Hue Sync Box Power Toggle'), $HueSyncScriptCategoryID, $content);
        $content = '<?php HUESYNC_Mode(' . $this->InstanceID . ', \'game\');';
        $this->CreateScript('Hue Sync Box Mode Game', $this->CreateIdent('Hue Sync Box Mode Game'), $HueSyncScriptCategoryID, $content);
        $content = '<?php HUESYNC_Mode(' . $this->InstanceID . ', \'video\');';
        $this->CreateScript('Hue Sync Box Mode Video', $this->CreateIdent('Hue Sync Box Mode Video'), $HueSyncScriptCategoryID, $content);
        $content = '<?php HUESYNC_Mode(' . $this->InstanceID . ', \'music\');';
        $this->CreateScript('Hue Sync Box Mode Music', $this->CreateIdent('Hue Sync Box Mode Music'), $HueSyncScriptCategoryID, $content);
        $content = '<?php
$mode = HUESYNC_GetLastSyncMode(' . $this->InstanceID . ');
$intensity = \'subtle\';
$response = HUESYNC_Intensity(' . $this->InstanceID . ', $mode, $intensity);';
        $this->CreateScript('Hue Sync Box Intensity Subtle', $this->CreateIdent('Hue Sync Box Intensity Subtle'), $HueSyncScriptCategoryID, $content);
        $content = '<?php
$mode = HUESYNC_GetLastSyncMode(' . $this->InstanceID . ');
$intensity = \'moderate\';
$response = HUESYNC_Intensity(' . $this->InstanceID . ', $mode, $intensity);';
        $this->CreateScript('Hue Sync Box Intensity Moderate', $this->CreateIdent('Hue Sync Box Intensity Moderate'), $HueSyncScriptCategoryID, $content);
        $content = '<?php
$mode = HUESYNC_GetLastSyncMode(' . $this->InstanceID . ');
$intensity = \'high\';
$response = HUESYNC_Intensity(' . $this->InstanceID . ', $mode, $intensity);';
        $this->CreateScript('Hue Sync Box Intensity High', $this->CreateIdent('Hue Sync Box Intensity High'), $HueSyncScriptCategoryID, $content);
        $content = '<?php
$mode = HUESYNC_GetLastSyncMode(' . $this->InstanceID . ');
$intensity = \'intense\';
$response = HUESYNC_Intensity(' . $this->InstanceID . ', $mode, $intensity);';
        $this->CreateScript('Hue Sync Box Intensity Intense', $this->CreateIdent('Hue Sync Box Intensity Intense'), $HueSyncScriptCategoryID, $content);
        $content = '<?php HUESYNC_LEDMode(' . $this->InstanceID . ', 1);';
        $this->CreateScript('Hue Sync Box LED Mode regular', $this->CreateIdent('Hue Sync Box LED Mode regular'), $HueSyncScriptCategoryID, $content);
        $content = '<?php HUESYNC_LEDMode(' . $this->InstanceID . ', 0);';
        $this->CreateScript('Hue Sync Box LED Mode off', $this->CreateIdent('Hue Sync Box LED Mode off'), $HueSyncScriptCategoryID, $content);
        $content = '<?php HUESYNC_LEDMode(' . $this->InstanceID . ', 2);';
        $this->CreateScript('Hue Sync Box LED Mode dimmed', $this->CreateIdent('Hue Sync Box LED Mode dimmed'), $HueSyncScriptCategoryID, $content);
        $content = '<?php HUESYNC_PreviousSyncMode(' . $this->InstanceID . ');';
        $this->CreateScript('Hue Sync Box Previous Sync Mode', $this->CreateIdent('Hue Sync Box Previous Sync Mode'), $HueSyncScriptCategoryID, $content);
        $content = '<?php HUESYNC_NextSyncMode(' . $this->InstanceID . ');';
        $this->CreateScript('Hue Sync Box Next Sync Mode', $this->CreateIdent('Hue Sync Box Next Sync Mode'), $HueSyncScriptCategoryID, $content);
        $content = '<?php HUESYNC_PreviousHDMISource(' . $this->InstanceID . ');';
        $this->CreateScript('Hue Sync Box Previous HDMI Source', $this->CreateIdent('Hue Sync Box Previous HDMI Source'), $HueSyncScriptCategoryID, $content);
        $content = '<?php HUESYNC_NextHDMISource(' . $this->InstanceID . ');';
        $this->CreateScript('Hue Sync Box Next HDMI Source', $this->CreateIdent('Hue Sync Box Next HDMI Source'), $HueSyncScriptCategoryID, $content);
        $content = '<?php HUESYNC_PreviousIntensity(' . $this->InstanceID . ');';
        $this->CreateScript('Hue Sync Box Previous Intensity', $this->CreateIdent('Hue Sync Box Previous Intensity'), $HueSyncScriptCategoryID, $content);
        $content = '<?php HUESYNC_NextIntensity(' . $this->InstanceID . ');';
        $this->CreateScript('Hue Sync Box Next Intensity', $this->CreateIdent('Hue Sync Box Next Intensity'), $HueSyncScriptCategoryID, $content);
    }

    protected function CreateScript($scriptname, $ident, $parent, $content)
    {
        $ScriptID           = @IPS_GetObjectIDByIdent($ident, $parent);
        if ($ScriptID === false) {
            $ScriptID = IPS_CreateScript(0);
            IPS_SetName($ScriptID, $scriptname);
            IPS_SetParent($ScriptID, $parent);
            IPS_SetIdent($ScriptID, $ident);
            IPS_SetScriptContent($ScriptID, $content);
        }
        return $ScriptID;
    }

    protected function CreateIdent($str)
    {
        $search  = [
            'ä',
            'ö',
            'ü',
            'ß',
            'Ä',
            'Ö',
            'Ü',
            '&',
            'é',
            'á',
            'ó',
            ' :)',
            ' :D',
            ' :-)',
            ' :P',
            ' :O',
            ' ;D',
            ' ;)',
            ' ^^',
            ' :|',
            ' :-/',
            ':)',
            ':D',
            ':-)',
            ':P',
            ':O',
            ';D',
            ';)',
            '^^',
            ':|',
            ':-/',
            '(',
            ')',
            '[',
            ']',
            '<',
            '>',
            '!',
            '"',
            '§',
            '$',
            '%',
            '&',
            '/',
            '(',
            ')',
            '=',
            '?',
            '`',
            '´',
            '*',
            "'",
            '-',
            ':',
            ';',
            '²',
            '³',
            '{',
            '}',
            '\\',
            '~',
            '#',
            '+',
            '.',
            ',',
            '=',
            ':',
            '=)', ];
        $replace = [
            'ae',
            'oe',
            'ue',
            'ss',
            'Ae',
            'Oe',
            'Ue',
            'und',
            'e',
            'a',
            'o',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '', ];

        $str = str_replace($search, $replace, $str);
        $str = str_replace(' ', '_', $str); // Replaces all spaces with underline.
        $how = '_';
        //$str = strtolower(preg_replace("/[^a-zA-Z0-9]+/", trim($how), $str));
        $str = preg_replace('/[^a-zA-Z0-9]+/', trim($how), $str);

        return $str;
    }

    protected function SendCommand($command, $type, $postfields = null)
    {
        if ($postfields !== null) {
            $data_json = json_encode($postfields);
        }
        if ($command == '/api/v1/device' && $type == 'GET') {
            $headers = [];
        } elseif ($command == '/api/v1/registrations') {
            $headers[] = "Accept-Charset: UTF-8";
            $headers[] = "Content-type: application/json;charset=\"UTF-8\"";
        } elseif ($type == 'PUT') {
            $headers[] = "Accept-Charset: UTF-8";
            $headers[] = "Content-type: application/json;charset=\"UTF-8\"";
            $headers[] = "Content-Length: " . strlen($data_json);
            $headers[] = "Authorization: Bearer " . $this->ReadAttributeString('AccessToken');
        } else {
            $headers[] = "Accept-Charset: UTF-8";
            $headers[] = "Content-type: application/json;charset=\"UTF-8\"";
            $headers[] = "Authorization: Bearer " . $this->ReadAttributeString('AccessToken');
        }

        $url = 'https://' . $this->ReadPropertyString('Host') . $command;

        $ch = curl_init();
        if ($type == 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        if ($type == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        if ($type == 'GET') {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_ENCODING, '');
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        if ($postfields !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
        }

        curl_setopt($ch, CURLOPT_URL, $url);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            trigger_error('Error:' . curl_error($ch));
        }
        $info       = curl_getinfo($ch);
        $header_out = curl_getinfo($ch, CURLINFO_HEADER_OUT);
        $this->SendDebug(__FUNCTION__, 'Header Out:' . $header_out, 0);
        curl_close($ch);

        return $this->getReturnValues($info, $result);
    }

    private function getReturnValues(array $info, string $result): array
    {
        $HeaderSize = $info['header_size'];

        $http_code = $info['http_code'];
        $this->SendDebug(__FUNCTION__, 'Response (http_code): ' . $http_code, 0);
        // Request Successfully	200	none	The request has been processed successfully. A JSON payload corresponding to the accessed URI (and credentials) is returned.
        // Invalid URI Path	404	none	Accessing URI path which is not supported
        // Authentication failed*	401	error	If credentials are missing or invalid, errors out.
        //*If credentials are missing, continues on to GET only the Configuration state when unauthenticated, to allow for device identification.
        // Internal error	500	none	Internal errors like out of memory

        $header = explode("\n", substr($result, 0, $HeaderSize));
        $this->SendDebug(__FUNCTION__, 'Response (header): ' . json_encode($header), 0);

        $body = substr($result, $HeaderSize);
        $this->SendDebug(__FUNCTION__, 'Response (body): ' . $body, 0);

        return ['http_code' => $http_code, 'header' => $header, 'body' => $body];
    }

    public function RequestAction($Ident, $Value)
    {
        if ($Ident === 'Mode') {
            if ($Value == 0) {
                $this->Mode('passthrough');
            }
            if ($Value == 1) {
                $this->Mode('powersave');
            }
            if ($Value == 2) {
                $this->Mode('video');
            }
            if ($Value == 3) {
                $this->Mode('music');
            }
            if ($Value == 4) {
                $this->Mode('game');
            }
        }
        if ($Ident === 'Brightness') {
            $this->Brightness($Value);
        }
        if ($Ident === 'Input') {
            if ($Value == 0) {
                $this->SendDebug('Input', 'HDMI 1 selected', 0);
                $this->SetHDMIInput(1);
            }
            if ($Value == 1) {
                $this->SendDebug('Input', 'HDMI 2 selected', 0);
                $this->SetHDMIInput(2);
            }
            if ($Value == 2) {
                $this->SendDebug('Input', 'HDMI 3 selected', 0);
                $this->SetHDMIInput(3);
            }
            if ($Value == 3) {
                $this->SendDebug('Input', 'HDMI 4 selected', 0);
                $this->SetHDMIInput(4);
            }
        }
        if ($Ident === 'Intensity') {
            $mode = $this->ReadAttributeString('lastSyncMode');
            if ($Value == 0) {
                $this->Intensity($mode, 'subtle');
            }
            if ($Value == 1) {
                $this->Intensity($mode, 'moderate');
            }
            if ($Value == 2) {
                $this->Intensity($mode, 'high');
            }
            if ($Value == 3) {
                $this->Intensity($mode, 'intense');
            }
        }
        if ($Ident === 'State') {
            if ($Value == true) {
                $this->PowerOn();
            }
            if ($Value == false) {
                $this->PowerOff();
            }
            $this->SendDebug('State', strval($Value) . ' selected', 0);
        }
        if ($Ident === 'ledMode') {
            $this->LEDMode($Value);
            $this->SendDebug('LEDMode', strval($Value) . ' selected', 0);
        }
        if ($Ident === 'syncActive') {
            $this->SyncActive($Value);
            $this->SendDebug('Sync Active', strval($Value) . ' selected', 0);
        }
        if ($Ident === 'hdmiActive') {
            $this->HDMIActive($Value);
            $this->SendDebug('HDMI Active', strval($Value) . ' selected', 0);
        }
        if ($Ident === 'input1_name') {
            $this->DefineInput1Name($Value);
        }
        if ($Ident === 'input2_name') {
            $this->DefineInput2Name($Value);
        }
        if ($Ident === 'input3_name') {
            $this->DefineInput3Name($Value);
        }
        if ($Ident === 'input4_name') {
            $this->DefineInput4Name($Value);
        }
        if ($Ident === 'cecPowersave') {
            $this->CEC_PowerStateDetection($Value);
        }
        if ($Ident === 'usbPowersave') {
            $this->USB_PowerStateDetection($Value);
        }
        if ($Ident === 'backlight_video') {
            $this->BackgroundLighting('video', $Value);
        }
        if ($Ident === 'backlight_game') {
            $this->BackgroundLighting('game', $Value);
        }
        if ($Ident === 'arcBypassMode') {
            $this->ARC_Bypass($Value);
        }
        if ($Ident === 'music_palette') {
            if ($Value == 0) {
                $this->Palette('happyEnergetic');
            }
            if ($Value == 1) {
                $this->Palette('happyCalm');
            }
            if ($Value == 2) {
                $this->Palette('melancholicCalm');
            }
            if ($Value == 3) {
                $this->Palette('melancholic Energetic');
            }
            if ($Value == 4) {
                $this->Palette('neutral');
            }
        }
        if ($Ident === 'inactivePowersave') {
            $this->HDMI_InactivityPowerState($Value);
        }


    }



    /***********************************************************
     * Configuration Form
     ***********************************************************/

    /**
     * build configuration form
     *
     * @return string
     */
    public function GetConfigurationForm()
    {
        // return current form
        return json_encode(
            [
                'elements' => $this->FormHead(),
                'actions'  => $this->FormActions(),
                'status'   => $this->FormStatus()]
        );
    }

    /**
     * return form configurations on configuration step
     *
     * @return array
     */
    protected function FormHead()
    {
        $access_token = $this->ReadAttributeString('AccessToken');
        $name         = $this->ReadAttributeString('name');
        $deviceType   = $this->ReadAttributeString('deviceType');
        // $uniqueId = $this->ReadAttributeString('uniqueId');
        $ipAddress                = $this->ReadAttributeString('ipAddress');
        $apiLevel                 = $this->ReadAttributeInteger('apiLevel');
        $firmwareVersion          = $this->ReadAttributeString('firmwareVersion');
        $buildNumber              = $this->ReadAttributeInteger('buildNumber');
        $lastCheckedUpdate        = $this->ReadAttributeString('lastCheckedUpdate');
        $updatableBuildNumber     = $this->ReadAttributeString('updatableBuildNumber');
        $updatableFirmwareVersion = $this->ReadAttributeString('updatableFirmwareVersion');
        $autoUpdateEnabled        = $this->ReadAttributeBoolean('autoUpdateEnabled');
        $autoUpdateTime           = $this->ReadAttributeInteger('autoUpdateTime');
        $list_visible             = false;
        $registrations_values     = [];
        if ($access_token != '') {
            $list_visible  = true;
            $registrations = json_decode($this->ReadAttributeString('registrations'));
            foreach ($registrations as $key => $registration) {
                $position               = $key;
                $appName                = $registration->appName; // User recognizable name of registered application
                $instanceName           = $registration->instanceName; // User recognizable name of application instance. Either a user name if single registration for user is shared over devices, or device name if each device uses a separate registration.
                $role                   = $registration->role; // admin/user
                $lastUsed               = $registration->lastUsed; // UTC time when this registration was last used
                $created                = $registration->created; // UTC time when this registration was created
                $registrations_values[] = [
                    'position'     => $position,
                    'appName'      => $appName,
                    'instanceName' => $instanceName,
                    'role'         => $role,
                    'lastUsed'     => $lastUsed,
                    'created'      => $created];
            }
        }
        $form = [
            [
                'type'  => 'Image',
                'image' => 'data:image/png;base64, iVBORw0KGgoAAAANSUhEUgAAAJ8AAACWCAYAAADAFFooAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA3ZpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMDY3IDc5LjE1Nzc0NywgMjAxNS8wMy8zMC0yMzo0MDo0MiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD0ieG1wLmRpZDpjNTVjOTc2OC1hM2U0LTVkNDUtOGMzZS0yZDk0NmQxNGY1NjkiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6NzE2OTgxQzczRUZCMTFFQUI3M0ZBOTU0MzBGODUwMjMiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6NzE2OTgxQzYzRUZCMTFFQUI3M0ZBOTU0MzBGODUwMjMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTUgKFdpbmRvd3MpIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6YzU1Yzk3NjgtYTNlNC01ZDQ1LThjM2UtMmQ5NDZkMTRmNTY5IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOmM1NWM5NzY4LWEzZTQtNWQ0NS04YzNlLTJkOTQ2ZDE0ZjU2OSIv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'],
            [
                'type'  => 'Label',
                'label' => 'Hue Sync Box'],
            [
                'name'    => 'Host',
                'type'    => 'ValidationTextBox',
                'caption' => 'IP Hue Sync Box'],
            [
                'name'    => 'UpdateInterval',
                'type'    => 'IntervalBox',
                'caption' => 'seconds'],
            [
                'type'     => 'List',
                'name'     => 'DeviceInfo',
                'caption'  => 'Device Info',
                'visible'  => true,
                'rowCount' => 1,
                'add'      => false,
                'delete'   => false,
                'sort'     => [
                    'column'    => 'name',
                    'direction' => 'ascending'],
                'columns'  => [
                    [
                        'name'    => 'name',
                        'caption' => 'name',
                        'width'   => 'auto',
                        'save'    => true,
                        'visible' => true,],
                    [
                        'name'    => 'deviceType',
                        'caption' => 'Device type',
                        'width'   => '150px',
                        'save'    => true,
                        'visible' => true,],
                    [
                        'name'    => 'apiLevel',
                        'caption' => 'API level',
                        'width'   => '100px',
                        'save'    => true,
                        'visible' => true],
                    [
                        'name'    => 'firmwareVersion',
                        'caption' => 'Firmware',
                        'width'   => '100px',
                        'save'    => true,
                        'visible' => true],
                    [
                        'name'    => 'buildNumber',
                        'caption' => 'Build Number',
                        'width'   => '150px',
                        'save'    => true,
                        'visible' => true],
                    [
                        'name'    => 'lastCheckedUpdate',
                        'caption' => 'Last Checked Update',
                        'width'   => '250px',
                        'save'    => true,
                        'visible' => true],
                    [
                        'name'    => 'updatableBuildNumber',
                        'caption' => 'Updatable Build Number',
                        'width'   => '200px',
                        'save'    => true,
                        'visible' => true],
                    [
                        'name'    => 'updatableFirmwareVersion',
                        'caption' => 'Updatable Firmware Version',
                        'width'   => '230px',
                        'save'    => true,
                        'visible' => true],
                    [
                        'name'    => 'autoUpdateEnabled',
                        'caption' => 'Auto Update Enabled',
                        'width'   => '150px',
                        'save'    => true,
                        'visible' => true,
                        'edit'    => [
                            'type' => 'CheckBox']],
                    [
                        'name'    => 'autoUpdateTime',
                        'caption' => 'Auto Update Time',
                        'width'   => '150px',
                        'save'    => true,
                        'visible' => true],],
                'values'   => [
                    [
                        'name'                     => $name,
                        'deviceType'               => $deviceType,
                        'apiLevel'                 => $apiLevel,
                        'firmwareVersion'          => $firmwareVersion,
                        'buildNumber'              => $buildNumber,
                        'lastCheckedUpdate'        => $lastCheckedUpdate,
                        'updatableBuildNumber'     => $updatableBuildNumber,
                        'updatableFirmwareVersion' => $updatableFirmwareVersion,
                        'autoUpdateEnabled'        => $autoUpdateEnabled,
                        'autoUpdateTime'           => $autoUpdateTime]]],
            [
                'type'    => 'ExpansionPanel',
                'caption' => 'Registrations',
                'visible' => $list_visible,
                'items'   => [
                    [
                        'type'     => 'List',
                        'name'     => 'registrations',
                        'caption'  => 'Registrations',
                        'visible'  => $list_visible,
                        'rowCount' => 20,
                        'sort'     => [
                            'column'    => 'position',
                            'direction' => 'ascending'],
                        'columns'  => [
                            [
                                'name'    => 'position',
                                'caption' => 'position',
                                'width'   => '100px',
                                'save'    => true,
                                'visible' => true],
                            [
                                'name'    => 'appName',
                                'caption' => 'App Name',
                                'width'   => '200px',
                                'save'    => true],
                            [
                                'name'    => 'instanceName',
                                'caption' => 'Instance Name',
                                'width'   => '200px',
                                'save'    => true,
                                'visible' => true],
                            [
                                'name'    => 'role',
                                'caption' => 'role',
                                'width'   => '200px',
                                'save'    => true,
                                'visible' => true],
                            [
                                'name'    => 'lastUsed',
                                'caption' => 'Last Used',
                                'width'   => '200px',
                                'save'    => true,
                                'visible' => true],
                            [
                                'name'    => 'created',
                                'caption' => 'created',
                                'width'   => '200px',
                                'save'    => true,
                                'visible' => true]],
                        'values'   => $registrations_values]]],
            [
                'type'    => 'ExpansionPanel',
                'caption' => 'Hue Sync Scripts',
                'visible' => true,
                'items'   => [
                    [
                        'name'    => 'ImportCategoryID',
                        'type'    => 'SelectCategory',
                        'caption' => 'category Hue Sync Box', ],
                    [
                        'type'    => 'Label',
                        'visible' => true,
                        'caption' => 'Create scripts for control of the Hue Sync Box'],
                    [
                        'name'     => 'HueSyncScript',
                        'type'     => 'CheckBox',
                        'caption'  => 'Create scripts',
                        'visible'  => true]
                    ]],
            [
                'type'    => 'ExpansionPanel',
                'caption' => 'Voice Control',
                'visible' => false,
                'items'   => [
                    [
                        'type'    => 'Label',
                        'visible' => true,
                        'caption' => 'Activate voice control by Alexa'],
                    [
                        'name'     => 'AlexaVoiceControl',
                        'type'     => 'CheckBox',
                        'caption'  => 'Alexa',
                        'visible'  => true,
                        'value'    => $this->ReadAttributeBoolean('AlexaVoiceControl'),
                        'onChange' => 'HUESYNC_SetVoiceControl($id, "Alexa");'],
                    [
                        'type'    => 'Label',
                        'visible' => true,
                        'caption' => 'Activate voice control by Google Assistant'],
                    [
                        'name'     => 'GoogleVoiceControl',
                        'type'     => 'CheckBox',
                        'caption'  => 'Google Assistant',
                        'visible'  => true,
                        'value'    => $this->ReadAttributeBoolean('GoogleVoiceControl'),
                        'onChange' => 'HUESYNC_SetVoiceControl($id, "Google");'],
                    [
                        'type'    => 'Label',
                        'visible' => true,
                        'caption' => 'Activate voice control by Siri'],
                    [
                        'name'     => 'SiriVoiceControl',
                        'type'     => 'CheckBox',
                        'caption'  => 'Siri',
                        'visible'  => true,
                        'value'    => $this->ReadAttributeBoolean('SiriVoiceControl'),
                        'onChange' => 'HUESYNC_SetVoiceControl($id, "Homekit");']]]];
        return $form;
    }

    /**
     * return form actions by token
     *
     * @return array
     */
    protected function FormActions()
    {
        $access_token = $this->ReadAttributeString('AccessToken');
        if ($access_token == '') {
            $form = [
                [
                    'type'    => 'Label',
                    'caption' => 'Register IP-Symcon on Philips Hue Sync Box'],
                [
                    'type'    => 'Label',
                    'caption' => 'Hold button on Philips Hue Sync Box for few seconds until Led is green, then press the button Registration'],
                [
                    'type'    => 'Button',
                    'caption' => 'Registration',
                    'onClick' => 'HUESYNC_Registration($id);'],];
        } else {
            $form = [
                [
                    'type'     => 'ExpansionPanel',
                    'caption'  => 'Settings',
                    'name'     => 'settings',
                    'visible'  => true,
                    'expanded' => false,
                    'items'    => [
                        [
                            'type'     => 'ExpansionPanel',
                            'caption'  => 'Entertainment Areas',
                            'name'     => 'EntertainmentSettings',
                            'visible'  => true,
                            'expanded' => false,
                            'items'    => $this->FormEntertainmentSettings()],
                        [
                            'type'     => 'ExpansionPanel',
                            'caption'  => 'HDMI Inputs',
                            'name'     => 'HDMIInputs',
                            'visible'  => true,
                            'expanded' => false,
                            'items'    => $this->FormHDMIInputsSettings()],
                        [
                            'type'     => 'ExpansionPanel',
                            'caption'  => 'Automatic Control',
                            'name'     => 'AutomaticControl',
                            'visible'  => true,
                            'expanded' => false,
                            'items'    => $this->FormAutomaticControlSettings()],
                        [
                            'type'     => 'ExpansionPanel',
                            'caption'  => 'Advanced Synchronization Settings',
                            'name'     => 'AdvancedSynchronizationSettings',
                            'visible'  => true,
                            'expanded' => false,
                            'items'    => $this->FormAdvancedSynchronizationSettings()],
                        [
                            'type'     => 'ExpansionPanel',
                            'caption'  => 'IR Codes',
                            'name'     => 'IRCodes',
                            'visible'  => true,
                            'expanded' => false,
                            'items'    => $this->FormIRCodes()]]]];
        }
        return $form;
    }

    protected function FormEntertainmentSettings()
    {
        $form = [
            [
                'type'    => 'Label',
                'visible' => true,
                'caption' => 'Select an entertainment area'],
            [
                'type'    => 'Label',
                'visible' => true,
                'caption' => 'Where do you want to use the Sync Box?'],
            [
                'type'    => 'Label',
                'visible' => true,
                'caption' => 'The Sync Box works with Hue entertainment areas. If necessary, create an entertainment area in the Philips Hue app!'],
            [
                'type'     => 'List',
                'name'     => 'EntertainmentAreas',
                'caption'  => 'Entertainment Areas',
                'visible'  => true,
                'rowCount' => 20,
                'sort'     => [
                    'column'    => 'name',
                    'direction' => 'ascending'],
                'columns'  => [
                    [
                        'name'    => 'groupId',
                        'caption' => 'groupId',
                        'width'   => '100px',
                        'save'    => true,
                        'visible' => false],
                    [
                        'name'    => 'name',
                        'caption' => 'name',
                        'width'   => '200px',
                        'save'    => true],
                    [
                        'name'    => 'numLights',
                        'caption' => 'number of lights',
                        'width'   => '200px',
                        'save'    => true,
                        'visible' => true],
                    [
                        'name'    => 'active',
                        'caption' => 'active',
                        'width'   => '200px',
                        'save'    => true,
                        'visible' => false],
                    [
                        'name'    => 'selected',
                        'caption' => 'selected',
                        'width'   => '200px',
                        'save'    => true,
                        'visible' => true,
                        'edit'    => [
                            'type' => 'CheckBox']]],
                'values'   => $this->GetEntertainmentZoneValues()]

        ];
        return $form;
    }

    protected function GetEntertainmentZoneValues()
    {
        $hue_groupId             = $this->ReadAttributeString('hue_groupId');
        $hue_groups_json         = $this->ReadAttributeString('hue_groups');
        $hue_groups              = json_decode($hue_groups_json);
        $EntertainmentZoneValues = [];
        foreach ($hue_groups as $key => $group) {
            if ($key == $hue_groupId) {
                $selected = true;
            } else {
                $selected = false;
            }
            $name                      = $group->name; // Friendly name of the entertainment group
            $numLights                 = $group->numLights; // Currently 0-10
            $active                    = $group->active;
            // owner Only exposed if active is true
            $EntertainmentZoneValues[] =
                ['groupId' => $key, 'name' => $name, 'numLights' => $numLights, 'active' => $active, 'selected' => $selected];
        }
        return $EntertainmentZoneValues;
    }

    protected function FormHDMIInputsSettings()
    {
        $form = [
            [
                'type'    => 'Label',
                'visible' => true,
                'caption' => 'Change the name of the HDMI input'],
            [
                'type'    => 'RowLayout',
                'visible' => true,
                'items'   => [
                    [
                        'type'    => 'ValidationTextBox',
                        'name'    => 'input1_name',
                        'visible' => true,
                        'caption' => 'HDMI 1',
                        'value'   => $this->ReadAttributeString('input1_name'),
                        'onClick' => 'HUESYNC_DefineInput1Name($id, $input1_name);'],
                    [
                        'name'     => 'input1_name_enabled',
                        'type'     => 'CheckBox',
                        'caption'  => 'Create Variable for Webfront',
                        'visible'  => true,
                        'value'    => $this->ReadAttributeBoolean('input1_name_enabled'),
                        'onChange' => 'HUESYNC_SetWebFrontVariable($id, "input1_name_enabled", $input1_name_enabled);'],]],
            [
                'type'    => 'RowLayout',
                'visible' => true,
                'items'   => [
                    [
                        'type'    => 'ValidationTextBox',
                        'name'    => 'input2_name',
                        'visible' => true,
                        'caption' => 'HDMI 2',
                        'value'   => $this->ReadAttributeString('input2_name'),
                        'onClick' => 'HUESYNC_DefineInput2Name($id, $input2_name);'],
                    [
                        'name'     => 'input2_name_enabled',
                        'type'     => 'CheckBox',
                        'caption'  => 'Create Variable for Webfront',
                        'visible'  => true,
                        'value'    => $this->ReadAttributeBoolean('input2_name_enabled'),
                        'onChange' => 'HUESYNC_SetWebFrontVariable($id, "input2_name_enabled", $input2_name_enabled);'],]],
            [
                'type'    => 'RowLayout',
                'visible' => true,
                'items'   => [
                    [
                        'type'    => 'ValidationTextBox',
                        'name'    => 'input3_name',
                        'visible' => true,
                        'caption' => 'HDMI 3',
                        'value'   => $this->ReadAttributeString('input3_name'),
                        'onClick' => 'HUESYNC_DefineInput3Name($id, $input3_name);'],
                    [
                        'name'     => 'input3_name_enabled',
                        'type'     => 'CheckBox',
                        'caption'  => 'Create Variable for Webfront',
                        'visible'  => true,
                        'value'    => $this->ReadAttributeBoolean('input3_name_enabled'),
                        'onChange' => 'HUESYNC_SetWebFrontVariable($id, "input3_name_enabled", $input3_name_enabled);'],]],
            [
                'type'    => 'RowLayout',
                'visible' => true,
                'items'   => [
                    [
                        'type'    => 'ValidationTextBox',
                        'name'    => 'input4_name',
                        'visible' => true,
                        'caption' => 'HDMI 4',
                        'value'   => $this->ReadAttributeString('input4_name'),
                        'onClick' => 'HUESYNC_DefineInput4Name($id, $input4_name);'],
                    [
                        'name'     => 'input4_name_enabled',
                        'type'     => 'CheckBox',
                        'caption'  => 'Create Variable for Webfront',
                        'visible'  => true,
                        'value'    => $this->ReadAttributeBoolean('input4_name_enabled'),
                        'onChange' => 'HUESYNC_SetWebFrontVariable($id, "input4_name_enabled", $input4_name_enabled);'],]]

        ];
        return $form;
    }

    protected function FormAutomaticControlSettings()
    {
        $form = [
            [
                'type'    => 'Label',
                'visible' => true,
                'caption' => 'Switches the inputs automatically'],
            [
                'type'    => 'Label',
                'visible' => true,
                'caption' => 'Automatic switch on / off'],
            [
                'type'    => 'RowLayout',
                'visible' => true,
                'items'   => [
                    [
                        'type'    => 'CheckBox',
                        'name'    => 'cecPowersave',
                        'visible' => true,
                        'caption' => 'Detect CEC power on status',
                        'value'   => boolval($this->ReadAttributeInteger('cecPowersave')),
                        'onClick' => 'HUESYNC_CEC_PowerStateDetection($id, $cecPowersave);'],
                    [
                        'name'     => 'cecPowersave_enabled',
                        'type'     => 'CheckBox',
                        'caption'  => 'Create Variable for Webfront',
                        'visible'  => true,
                        'value'    => $this->ReadAttributeBoolean('cecPowersave_enabled'),
                        'onChange' => 'HUESYNC_SetWebFrontVariable($id, "cecPowersave_enabled", $cecPowersave_enabled);'],]],
            [
                'type'    => 'Label',
                'visible' => true,
                'caption' => 'Use CEC to detect when your TV is on to turn the Sync Box on or off accordingly.'],
            [
                'type'    => 'RowLayout',
                'visible' => true,
                'items'   => [
                    [
                        'type'    => 'CheckBox',
                        'name'    => 'usbPowersave',
                        'visible' => true,
                        'caption' => 'Detect USB power on status',
                        'value'   => $this->ReadAttributeInteger('usbPowersave'),
                        'onClick' => 'HUESYNC_USB_PowerStateDetection($id, $usbPowersave);'],
                    [
                        'name'     => 'usbPowersave_enabled',
                        'type'     => 'CheckBox',
                        'caption'  => 'Create Variable for Webfront',
                        'visible'  => true,
                        'value'    => $this->ReadAttributeBoolean('usbPowersave_enabled'),
                        'onChange' => 'HUESYNC_SetWebFrontVariable($id, "usbPowersave_enabled", $usbPowersave_enabled);'],]],
            [
                'type'    => 'Label',
                'visible' => true,
                'caption' => 'Connect a USB cable to your TV to recognize its switched-on status and to be able to switch the Sync Box on and off accordingly. Deactivating this function does not deactivate the automatic switch-on.'],
            [
                'type'    => 'RowLayout',
                'visible' => true,
                'items'   => [
                    [
                        'type'     => 'CheckBox',
                        'name'     => 'inactivePowersave',
                        'visible'  => true,
                        'caption'  => 'HDMI inactive for x min',
                        'value'    => $this->ReadAttributeInteger('inactivePowersave'),
                        'onChange' => 'HUESYNC_HDMI_InactivityPowerState($id, $inactivePowersave);'],
                    [
                        'name'     => 'inactivePowersave_enabled',
                        'type'     => 'CheckBox',
                        'caption'  => 'Create Variable for Webfront',
                        'visible'  => true,
                        'value'    => $this->ReadAttributeBoolean('inactivePowersave_enabled'),
                        'onChange' => 'HUESYNC_SetWebFrontVariable($id, "inactivePowersave_enabled", $inactivePowersave_enabled);'],]]


        ];
        return $form;
    }

    protected function FormAdvancedSynchronizationSettings()
    {
        $form = [
            [
                'type'    => 'Label',
                'visible' => true,
                'caption' => 'Minimum brightness'],
            [
                'type'    => 'Label',
                'visible' => true,
                'caption' => 'Backlight'],
            [
                'type'    => 'RowLayout',
                'visible' => true,
                'items'   => [
                    [
                        'type'    => 'CheckBox',
                        'name'    => 'backlight_video',
                        'visible' => true,
                        'caption' => 'Video',
                        'value'   => $this->ReadAttributeBoolean('backlight_video'),
                        'onClick' => 'HUESYNC_BackgroundLighting($id, "video", $backlight_video);'],
                    [
                        'name'     => 'backlight_video_enabled',
                        'type'     => 'CheckBox',
                        'caption'  => 'Create Variable for Webfront',
                        'visible'  => true,
                        'value'    => $this->ReadAttributeBoolean('backlight_video_enabled'),
                        'onChange' => 'HUESYNC_SetWebFrontVariable($id, "backlight_video_enabled", $backlight_video_enabled);'],]],
            [
                'type'    => 'RowLayout',
                'visible' => true,
                'items'   => [
                    [
                        'type'    => 'CheckBox',
                        'name'    => 'backlight_game',
                        'visible' => true,
                        'caption' => 'Game',
                        'value'   => $this->ReadAttributeBoolean('backlight_game'),
                        'onClick' => 'HUESYNC_BackgroundLighting($id, "game", $backlight_game);'],
                    [
                        'name'     => 'backlight_game_enabled',
                        'type'     => 'CheckBox',
                        'caption'  => 'Create Variable for Webfront',
                        'visible'  => true,
                        'value'    => $this->ReadAttributeBoolean('backlight_game_enabled'),
                        'onChange' => 'HUESYNC_SetWebFrontVariable($id, "backlight_game_enabled", $backlight_game_enabled);'],]],
            [
                'type'    => 'Label',
                'visible' => true,
                'caption' => 'Set a minimum brightness for the background brightness visible in each synchronization mode, even if the screen is black.'],
            [
                'type'    => 'Label',
                'visible' => true,
                'caption' => 'ARC bypass'],
            [
                'type'    => 'RowLayout',
                'visible' => true,
                'items'   => [
                    [
                        'type'     => 'CheckBox',
                        'name'     => 'arcBypassMode',
                        'visible'  => true,
                        'caption'  => 'Activate ARC bypass',
                        'value'    => boolval($this->ReadAttributeInteger('arcBypassMode')),
                        'onChange' => 'HUESYNC_ARC_Bypass($id, $arcBypassMode);'],
                    [
                        'name'     => 'arcBypassMode_enabled',
                        'type'     => 'CheckBox',
                        'caption'  => 'Create Variable for Webfront',
                        'visible'  => true,
                        'value'    => $this->ReadAttributeBoolean('arcBypassMode_enabled'),
                        'onChange' => 'HUESYNC_SetWebFrontVariable($id, "arcBypassMode_enabled", $arcBypassMode_enabled);'],]],
            [
                'type'    => 'Label',
                'visible' => true,
                'caption' => 'When your sync box is connected between an AV receiver and TV, enable this setting to allow the ARC signal to properly pass through. Note: This will only work with 1 connected input.'],

        ];
        return $form;
    }

    protected function FormIRCodes()
    {
        $form = [
            [
                'type'     => 'List',
                'name'     => 'IRCodesList',
                'caption'  => 'IR Codes',
                'visible'  => true,
                'rowCount' => 20,
                'sort'     => [
                    'column'    => 'name',
                    'direction' => 'ascending'],
                'columns'  => [
                    [
                        'name'    => 'ircode',
                        'caption' => 'ir code',
                        'width'   => '100px',
                        'save'    => true,
                        'visible' => true],
                    [
                        'name'    => 'name',
                        'caption' => 'name',
                        'width'   => '200px',
                        'save'    => true],
                    [
                        'name'    => 'commandir',
                        'caption' => 'command',
                        'width'   => '200px',
                        'save'    => true,
                        'visible' => true]],
                'values'   => $this->GetIRCodeList()]

        ];
        return $form;
    }

    protected function GetIRCodeList()
    {
        $codes = $this->ReadAttributeString('codes');
        $this->SendDebug('Get IR Codes', $codes, 0);
        $ir_codes              = json_decode($codes);
        $IRCodeValues = [];
        foreach ($ir_codes as $key => $ir_code) {
            $name                      = $ir_code->name; // Friendly name of the ir code
            $execution                = $ir_code->execution;
            foreach ($execution as $execution_key => $command_value) {
                $command_ir = $execution_key . " " . strval($command_value);
            }
            $IRCodeValues[] =
                ['ircode' => $key, 'name' => $name, 'commandir' => $command_ir];
        }
        $this->SendDebug('IR code list values', json_encode($IRCodeValues), 0);
        return $IRCodeValues;
    }

    /**
     * return from status
     *
     * @return array
     */
    protected function FormStatus()
    {
        $form = [
            [
                'code'    => IS_CREATING,
                'icon'    => 'inactive',
                'caption' => 'Creating instance.'],
            [
                'code'    => IS_ACTIVE,
                'icon'    => 'active',
                'caption' => 'Hue Sync Box created.'],
            [
                'code'    => IS_INACTIVE,
                'icon'    => 'inactive',
                'caption' => 'interface closed.'],
            [
                'code'    => 201,
                'icon'    => 'error',
                'caption' => 'could not connect to device'],
            [
                'code'    => 202,
                'icon'    => 'error',
                'caption' => 'unkown error'],
            [
                'code'    => 203,
                'icon'    => 'error',
                'caption' => 'IP adress is not valid'],
            [
                'code'    => 204,
                'icon'    => 'error',
                'caption' => 'password must not be empty'],
            [
                'code'    => 205,
                'icon'    => 'error',
                'caption' => 'IP adress must not be empty'],
            [
                'code'    => 218,
                'icon'    => 'error',
                'caption' => 'No valid Hue Sync Box IP adress or host.']];

        return $form;
    }
}