<?php


namespace App\Helpers;


if(!function_exists('transform_organization_asset')) {
    function transform_organization_asset($organization_asset,$asset) {
        return [
            'asset_uuid' => $asset['uuid'],
            'url' => url('app/' . $asset['path']),
            'asset_name' => $asset['asset_name'],
            'organization_asset_uuid' => $organization_asset['uuid']
        ];
    }
}

if(!function_exists('transform_event_asset')) {
    function transform_event_asset($event_asset, $asset) {
        return [
            'asset_uuid' => $asset['uuid'],
            'url' => url('app/' . $asset['path']),
            'asset_name' => $asset['asset_name'],
            'organization_asset_uuid' => $event_asset['uuid']
        ];
    }
}

if (!function_exists('isDate')) {
    //check if array key exist and return it's value otherwise return null
    function isDate($value)
    {
        if (!$value) {
            return false;
        }

        try {
            new \DateTime($value);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
