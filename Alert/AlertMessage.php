<?php namespace IO\Utilities\Alert;

class AlertMessage 
{
    public static function formatMany($logs, $rule, $device) 
    {
        $messages = [];
        foreach ($logs as $key => $log) {
            $messages[$key] = self::format($log, $rule, $device);
        }

        return $messages;
    }

    public static function format($log, $rule, $device) 
    {
        // Mar 16 12:00 am
        // 2. Th, Stue: CO2 exceeding 800 ppm and a high of 856 ppm within the alert period.
        // 
        // <p>Mar 17 06:00 pm<br>2. Th, Stue: CO<sub>2</sub> exceeding 800 ppm and a high of 843 ppm within the alert period. </p>

        $unit = self::getUnit($device['type']);
        $token['date'] = date('M d h:i a' , $log['reading'][1]);
        $token['room'] = $device['room'];
        $token['deviceName'] = self::getUnitName($device['type']);
        
        if ($log['range']['min'] >= $rule['above']) {
            $token['range'] = 'exceeding ' . $rule['above'] . $unit . ' and a high of ' . $log['range']['max'] . $unit;
        } else {
            $token['range'] = 'less than ' . $rule['below'] . $unit . ' and a low of ' . $log['range']['min'] . $unit;
        }   

        $pattern = "@date<br/>@room: @deviceName @range within the alert period.";

        $message = $pattern;
        foreach ($token as $key => $value) {
            $message = str_replace('@'.$key, $value, $message);
        }

        return $message;
    }

    public static function getUnitName($deviceType)
    {
        switch (strtolower($deviceType)) {
            case 'co2':
                $unit = 'CO<sub>2</sub>';
                break;
            default:
                $unit = ucfirst($deviceType);
        }

        return $unit;
    }

    public static function getUnit($deviceType)
    {
        $unit = '';

        switch (strtolower($deviceType)) {
            case 'co2':
                $unit = ' ppm';
                break;
            case 'humidity':
                $unit = '%';
                break;
            case 'temperature':
                $unit = '°C';
                break;
            case 'noise':
                $unit = ' db';
                break;
        }

        return $unit;
    }
}