<?php
namespace Lsia;

/**
 * 
 * Description of Utils
 *
 * @author rotimi
 * 
 */
class Utils {
    
    public static function arrayGet($potentialArray, string $key, $defaultVal='') {
        
        return (is_array($potentialArray) && array_key_exists($key, $potentialArray))
                ? $potentialArray[$key]
                : $defaultVal;
    }

    public static function getNullIfEmpty($val, bool $treatEmptyArrayAsEmpty=true) {
        
        if(
            empty($val) && !is_object($val) 
            && ( !is_array($val) || (is_array($val) && $treatEmptyArrayAsEmpty))
            && !is_bool($val) && $val !== 0 && $val !== '0'
        ) {
            return null;
        }
        
        return $val;
    }
    
    public static function getDefaultIfEmpty($val, $default) {
        
        return static::getNullIfEmpty($val) !== null ? $val : $default;
    }
    
    /**
     * 
     * @param bool $expr an expression to evaluate
     * @param mixed $trueVal Value to be returned if $expr is true
     *                      WARNING: This method may generate a Notice, Warning or Exception if $trueVal in some situations
     *                      FOR example if $expr is array_key_exists('aKey', $arr) and you pass $arr['aKey'] as $trueVal
     *                      then a Notice, Warning or Exception will be emitted when array_key_exists('aKey', $arr) === false
     * 
     * @param mixed $defaultVal Value to be returned if $expr is false
     * 
     * @return mixed
     */
    public static function getValIfTrueOrGetDefault(bool $expr, $trueVal, $defaultVal='') {
        
        return $expr ? $trueVal : $defaultVal;
    }
    
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
    
    public static function psr7RequestObjToString(
        \Psr\Http\Message\ServerRequestInterface $req, 
        array $request_attribute_keys_to_skip=['route','routeInfo'],
        $skip_req_attribs=false,
        $skip_req_body=false,
        $skip_req_cookie_params=false,
        $skip_req_headers=false,
        $skip_req_method=false,
        $skip_req_proto_ver=false,
        $skip_req_query_params=false,
        $skip_req_target=false,
        $skip_req_server_params=false,
        $skip_req_uploaded_files=true,
        $skip_req_uri=false
    ) {
        return s3MVC_psr7RequestObjToString(
                    $req, 
                    $request_attribute_keys_to_skip,
                    $skip_req_attribs,
                    $skip_req_body,
                    $skip_req_cookie_params,
                    $skip_req_headers,
                    $skip_req_method,
                    $skip_req_proto_ver,
                    $skip_req_query_params,
                    $skip_req_target,
                    $skip_req_server_params,
                    $skip_req_uploaded_files,
                    $skip_req_uri
                );
    }
    
    public static function psr7UploadedFileToString(\Psr\Http\Message\UploadedFileInterface $file) {
        
        return s3MVC_psr7UploadedFileToString($file);
    }
    
    public static function isEmptyString($string) {
        
        return empty($string) && mb_strlen( ''.$string, 'UTF-8') <= 0;
    }
    
    public static function displayFieldErrors(string $field, array $errors): string {
        
        $messages = '';
        
        if(isset($errors[$field])) {
            
            foreach($errors[$field] as $error) {
                
                $errStr = ((string)$error);
                $messages .= "<span class=\"helper-text red-text\">{$errStr}</span>".PHP_EOL;
                
            }
        }
        
        return $messages;
    }
    
    public static function isCountableWithData($var) {

        return is_countable($var) && (count($var) > 0);
    }
}
