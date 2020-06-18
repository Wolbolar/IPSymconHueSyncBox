<?php

declare(strict_types=1);

class HueSyncBoxDiscovery extends IPSModule
{
    public function Create()
    {
        //Never delete this line!
        parent::Create();
        $this->RegisterAttributeString('devices', '[]');

        //we will wait until the kernel is ready
        $this->RegisterMessage(0, IPS_KERNELMESSAGE);
        $this->RegisterMessage(0, IPS_KERNELSTARTED);
        $this->RegisterTimer('Discovery', 0, 'HUESYNCDiscovery_Discover($_IPS[\'TARGET\']);');
    }

    /**
     * Interne Funktion des SDK.
     */
    public function ApplyChanges()
    {
        //Never delete this line!
        parent::ApplyChanges();

        if (IPS_GetKernelRunlevel() !== KR_READY) {
            return;
        }

        $this->StartDiscovery();

        // Status Error Kategorie zum Import auswählen
        $this->SetStatus(IS_ACTIVE);
    }

    public function MessageSink($TimeStamp, $SenderID, $Message, $Data)
    {
        switch ($Message) {
            case IM_CHANGESTATUS:
                if ($Data[0] === IS_ACTIVE) {
                    $this->StartDiscovery();
                }
                break;

            case IPS_KERNELMESSAGE:
                if ($Data[0] === KR_READY) {
                    $this->StartDiscovery();
                }
                break;
            case IPS_KERNELSTARTED:
                $this->StartDiscovery();
                break;

            default:
                break;
        }
    }

    private function StartDiscovery()
    {
        if (empty($this->DiscoverDevices())) {
            $this->SendDebug('Discover:', 'could not find Hue Sync Box info', 0);
        } else {
            $this->WriteAttributeString('devices', json_encode($this->DiscoverDevices()));
        }
        $this->SetTimerInterval('Discovery', 300000);
    }

    /**
     * Liefert alle Geräte.
     *
     * @return array configlist all devices
     */
    private function Get_ListConfiguration()
    {
        $config_list      = [];
        $HueSyncBoxIDList = IPS_GetInstanceListByModuleID('{716FA5CE-2292-8EA5-78F9-8B245EFAF0A7}'); // Hue Sync Box Devices
        $devices          = $this->DiscoverDevices();
        $this->SendDebug('Discovered Hue Sync Boxes', json_encode($devices), 0);
        if (!empty($devices)) {
            foreach ($devices as $device) {
                $instanceID = 0;
                $name       = $device['name'];
                $hostname   = $device['hostname'];
                $host       = $device['host'];
                $devicetype = $device['devicetype'];
                $uniqueid   = $device['uniqueid'];

                $device_id = 0;
                foreach ($HueSyncBoxIDList as $HueSyncBoxID) {
                    if ($host == IPS_GetProperty($HueSyncBoxID, 'Host')) {
                        $HueSyncBox_name = IPS_GetName($HueSyncBoxID);
                        $this->SendDebug(
                            'Hue Sync Box Discovery', 'Hue Sync Box found: ' . utf8_decode($HueSyncBox_name) . ' (' . $HueSyncBoxID . ')', 0
                        );
                        $instanceID = $HueSyncBoxID;
                    }
                }

                $config_list[] = [
                    'instanceID' => $instanceID,
                    'id'         => $device_id,
                    'name'       => $name,
                    'hostname'   => $hostname,
                    'host'       => $host,
                    'devicetype' => $devicetype,
                    'uniqueid'   => $uniqueid,
                    'create'     => [
                        [
                            'moduleID'      => '{716FA5CE-2292-8EA5-78F9-8B245EFAF0A7}',
                            'configuration' => [
                                'huesync_name'     => $name,
                                'deviceType'       => $devicetype,
                                'huesync_uniqueId' => $uniqueid,
                                'Host'             => $host,],],],];
            }
        }
        return $config_list;
    }

    private function DiscoverDevices(): array
    {
        $devices = $this->scan();
        $this->SendDebug('Discover Response:', json_encode($devices), 0);
        $huesync_infos = $this->GetHueSyncBoxInfo($devices);
        if (empty($huesync_infos)) {
            $this->SendDebug('Discover:', 'could not find Hue Sync Box info', 0);
        } else {
            foreach ($huesync_infos as $device) {
                $this->SendDebug('name:', $device['name'], 0);
                $this->SendDebug('hostname:', $device['hostname'], 0);
                $this->SendDebug('host:', $device['host'], 0);
                $this->SendDebug('port:', $device['port'], 0);
                $this->SendDebug('devicetype:', $device['devicetype'], 0);
                $this->SendDebug('uniqueid:', $device['uniqueid'], 0);
            }
        }
        return $huesync_infos;
    }

    protected function GetHueSyncBoxInfo($devices)
    {
        $mDNSInstanceID = $this->GetDNSSD();
        $huesync_info   = [];
        foreach ($devices as $key => $huesync) {
            $response = ZC_QueryService($mDNSInstanceID, $huesync['Name'], $huesync['Type'], $huesync['Domain']);
            foreach ($response as $data) {
                $name     = str_ireplace('._huesync._tcp.local.', '', $data["Name"]);
                $hostname = str_ireplace('.local.', '', $data["Host"]);
                $port     = $data["Port"];
                $ip       = $data["IPv4"][0];
                $fields   = $data["TXTRecords"];
                foreach ($fields as $field) {
                    $path = '';
                    if (strpos($field, "path=") === 0) {
                        $path = str_ireplace('path=', '', $field);
                    }
                    if (strpos($field, "uniqueid=") === 0) {
                        $uniqueid = str_ireplace('uniqueid=', '', $field);
                    }
                    if (strpos($field, "devicetype=") === 0) {
                        $devicetype = str_ireplace('devicetype=', '', $field);
                    }
                    if (strpos($field, "name=") === 0) {
                        $name = str_ireplace('name=', '', $field);
                    }
                }
            }
            $huesync_info[$key] = [
                'name'       => $name,
                'hostname'   => $hostname,
                'host'       => $ip,
                'port'       => $port,
                'devicetype' => $devicetype,
                'uniqueid'   => $uniqueid,
                'path'       => $path];
        }

        return $huesync_info;
    }

    public function scan()
    {
        $mDNSInstanceID = $this->GetDNSSD();
        $huesync_boxes  = ZC_QueryServiceType($mDNSInstanceID, '_huesync._tcp', '');
        return $huesync_boxes;
    }

    private function GetDNSSD()
    {
        $mDNSInstanceIDs = IPS_GetInstanceListByModuleID('{780B2D48-916C-4D59-AD35-5A429B2355A5}');
        $mDNSInstanceID  = $mDNSInstanceIDs[0];
        return $mDNSInstanceID;
    }

    public function GetDevices()
    {
        $devices = $this->ReadPropertyString('devices');

        return $devices;
    }

    public function Discover()
    {
        if (empty($this->DiscoverDevices())) {
            $devices = '';
        } else {
            $this->LogMessage($this->Translate('Background Discovery of Philips Hue Sync Box'), KL_NOTIFY);
            $this->WriteAttributeString('devices', json_encode($this->DiscoverDevices()));
            $devices = json_encode($this->DiscoverDevices());
        }
        return $devices;
    }

    /***********************************************************
     * Configuration Form
     ***********************************************************/

    /**
     * build configuration form.
     *
     * @return string
     */
    public function GetConfigurationForm()
    {
        // return current form
        $Form = json_encode(
            [
                'elements' => $this->FormElements(),
                'actions'  => $this->FormActions(),
                'status'   => $this->FormStatus(),]
        );
        $this->SendDebug('FORM', $Form, 0);
        $this->SendDebug('FORM', json_last_error_msg(), 0);

        return $Form;
    }

    /**
     * return form configurations on configuration step.
     *
     * @return array
     */
    protected function FormElements()
    {
        $form = [
            [
                'type'  => 'Image',
                'image' => 'data:image/png;base64, iVBORw0KGgoAAAANSUhEUgAAAJ8AAACWCAYAAADAFFooAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA3ZpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMDY3IDc5LjE1Nzc0NywgMjAxNS8wMy8zMC0yMzo0MDo0MiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD0ieG1wLmRpZDpjNTVjOTc2OC1hM2U0LTVkNDUtOGMzZS0yZDk0NmQxNGY1NjkiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6NzE2OTgxQzczRUZCMTFFQUI3M0ZBOTU0MzBGODUwMjMiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6NzE2OTgxQzYzRUZCMTFFQUI3M0ZBOTU0MzBGODUwMjMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTUgKFdpbmRvd3MpIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6YzU1Yzk3NjgtYTNlNC01ZDQ1LThjM2UtMmQ5NDZkMTRmNTY5IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOmM1NWM5NzY4LWEzZTQtNWQ0NS04YzNlLTJkOTQ2ZDE0ZjU2OSIv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']];

        return $form;
    }

    /**
     * return form actions by token.
     *
     * @return array
     */
    protected function FormActions()
    {
        $form = [
            [
                'name'     => 'HueSyncBoxDiscovery',
                'type'     => 'Configurator',
                'rowCount' => 20,
                'add'      => false,
                'delete'   => true,
                'sort'     => [
                    'column'    => 'name',
                    'direction' => 'ascending',],
                'columns'  => [
                    [
                        'caption' => 'ID',
                        'name'    => 'id',
                        'width'   => '200px',
                        'visible' => false,],
                    [
                        'caption' => 'name',
                        'name'    => 'name',
                        'width'   => 'auto',],
                    [
                        'caption' => 'hostname',
                        'name'    => 'hostname',
                        'width'   => '400px',],
                    [
                        'caption' => 'host',
                        'name'    => 'host',
                        'width'   => '400px',],
                    [
                        'caption' => 'devicetype',
                        'name'    => 'devicetype',
                        'width'   => '400px',],],
                'values'   => $this->Get_ListConfiguration(),],];
        return $form;
    }

    /**
     * return from status.
     *
     * @return array
     */
    protected function FormStatus()
    {
        $form = [
            [
                'code'    => IS_CREATING,
                'icon'    => 'inactive',
                'caption' => 'Creating instance.',],
            [
                'code'    => IS_ACTIVE,
                'icon'    => 'active',
                'caption' => 'Philips Hue Sync Box Discovery created.',],
            [
                'code'    => IS_INACTIVE,
                'icon'    => 'inactive',
                'caption' => 'interface closed.',],
            [
                'code'    => 201,
                'icon'    => 'inactive',
                'caption' => 'Please follow the instructions.',],];

        return $form;
    }
}
