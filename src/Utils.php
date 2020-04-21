<?php
namespace Lsia;

/**
 * Description of Utils
 *
 * @author rotimi
 */
class Utils {

    public static function bytesToHumanReadable($bytes, $decimalPlaces = 2) {

        $type = array("bytes", "KB", "MB", "GB", "TB", "PB", "EXB", "ZB", "YB");
        $index = 0;

        while ($bytes >= 1024) {

            $bytes /= 1024;
            $index++;
        }

        $formattedBytes = number_format(((float) $bytes), $decimalPlaces);

        return ("" . $formattedBytes . " " . $type[$index]);
    }

    public static function getListOfFileSystems() {

        $mounted_file_systems = [];
        exec('findmnt -l -o TARGET', $mounted_file_systems);
        array_shift($mounted_file_systems); // remove output header
        sort($mounted_file_systems);

        return $mounted_file_systems;
    }

    public static function generateDiskUsageData() {

        $diskUsageData = [];

        foreach (static::getListOfFileSystems() as $mounted_file_system) {

            $freeSpace = disk_free_space($mounted_file_system);
            $totalSpace = disk_total_space($mounted_file_system);
            $usedSpacePercentage =  ($totalSpace <= 0) ? 0 :  (($totalSpace - $freeSpace) / $totalSpace) *  100;

            $diskUsageData[] = [
                'fs_name' => $mounted_file_system,
                'disk_free_space' => $freeSpace,
                'disk_total_space' => $totalSpace,
                'used_space_percent' => $usedSpacePercentage,
            ];
        }

        return $diskUsageData;
    }

    public static function generateDiskUsageDataHumanReadable() {

        $diskUsageData = static::generateDiskUsageData();

        foreach ($diskUsageData as $key => $data) {

            $diskUsageData[$key]['disk_free_space'] = static::bytesToHumanReadable($data['disk_free_space']);
            $diskUsageData[$key]['disk_total_space'] = static::bytesToHumanReadable($data['disk_total_space']);
        }

        return $diskUsageData;
    }
}
