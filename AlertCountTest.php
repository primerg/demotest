<?php
// use IO\Alert\Config as AlertConfig;
use IO\Utilities\Alert\CounterDefault;
use IO\Utilities\Alert\AlertMessage;

class AlertCountTest extends PHPUnit_Framework_TestCase {

    public function testCount()
    {
        date_default_timezone_set ('UTC');

        $config = [
            'below'  => '800',
            'above'  => '1000',
        ];

        $data = [
            1426978020 => [1426978020, 996],      // G
            1426978380 => [1426978380, 1118],     // R <-
            1426978740 => [1426978740, 1037],     // R
            1426981800 => [1426981800, 615],      // R
            1426979460 => [1426979460, 800],      // G
            1426979820 => [1426979820, 801],      // G
            1426980180 => [1426980180, 1175],     // R <-
            1426980540 => [1426980540, 626],      // R
            1426980900 => [1426980900, 690],      // R
            1426981260 => [1426981260, 500],      // R
            1426981620 => [1426981620, 1121],     // R
            1426981980 => [1426981980, 1253],     // R
            1426982340 => [1426982340, 799],      // R 2015-03-21
            1426982700 => [1426982700, 799],      // R <- next day, mark 2015-03-22 00:05:00
            1426983000 => [1426983000, 798],      // R
            1426983300 => [1426983300, 801],      // G
            1426983600 => [1426983600, 801],      // G
        ];

        $counter = new CounterDefault($data, $config);
        $counter->count();

        $expected = [
            1426978380 => [
                'reading' => [1118, 1426978380],
                'color' => 'Red',
                'formattedDate' => '2015-03-21 22:53:00',
                'range' => [
                    'min' => 615,
                    'max' => 1118
                ]
            ],
            1426980180 => [
                'reading' => [1175, 1426980180],
                'color' => 'Red',
                'formattedDate' => '2015-03-21 23:23:00',
                'range' => [
                    'min' => 500,
                    'max' => 1253
                ]
            ],
            1426982700 => [
                'reading' => [799, 1426982700],
                'color' => 'Red',
                'formattedDate' => '2015-03-22 00:05:00',
                'range' => [
                    'min' => 798,
                    'max' => 799
                ]
            ]
        ];

        return;

        $result = $counter->log();
        $this->assertEquals($expected, $result);

        $result = $counter->stat['red'];
        $this->assertEquals(3, $result);        

        $result = $counter->stat['yellow'];
        $this->assertEquals(0, $result);
    }

    public function testStringMessage()
    {
        $config = [
            'below'  => '800',
            'above'  => '1000',
        ];

        $logs = [
            1426978380 => [
                'reading' => [1118, 1426978380],
                'color' => 'Red',
                'formattedDate' => '2015-03-21 22:53:00',
                'range' => [
                    'min' => 1030,
                    'max' => 1118
                ]
            ],
            1426980180 => [
                'reading' => [1175, 1426980180],
                'color' => 'Red',
                'formattedDate' => '2015-03-21 23:23:00',
                'range' => [
                    'min' => 500,
                    'max' => 1253
                ]
            ]
        ];

        $device = [
            'room' => 'Room',
            'type' => 'co2'
        ];

        $result = AlertMessage::formatMany($logs, $config, $device);
        $expected = [
            1426978380 => 'Mar 21 10:53 pm<br/>Room: CO<sub>2</sub> exceeding 1000 ppm and a high of 1118 ppm within the alert period.',
            1426980180 => 'Mar 21 11:23 pm<br/>Room: CO<sub>2</sub> less than 800 ppm and a low of 500 ppm within the alert period.',
        ];

        $this->assertEquals($expected, $result);

        $device = [
            'room' => 'Room',
            'type' => 'temperature'
        ];

        $result = AlertMessage::formatMany($logs, $config, $device);
        $expected = [
            1426978380 => 'Mar 21 10:53 pm<br/>Room: Temperature exceeding 1000°C and a high of 1118°C within the alert period.',
            1426980180 => 'Mar 21 11:23 pm<br/>Room: Temperature less than 800°C and a low of 500°C within the alert period.',
        ];

        $this->assertEquals($expected, $result);

        $device = [
            'room' => 'Room',
            'type' => 'noise'
        ];

        $result = AlertMessage::formatMany($logs, $config, $device);
        $expected = [
            1426978380 => 'Mar 21 10:53 pm<br/>Room: Noise exceeding 1000 db and a high of 1118 db within the alert period.',
            1426980180 => 'Mar 21 11:23 pm<br/>Room: Noise less than 800 db and a low of 500 db within the alert period.',
        ];

        $this->assertEquals($expected, $result);

        $device = [
            'room' => 'Room',
            'type' => 'humidity'
        ];

        $result = AlertMessage::formatMany($logs, $config, $device);
        $expected = [
            1426978380 => 'Mar 21 10:53 pm<br/>Room: Humidity exceeding 1000% and a high of 1118% within the alert period.',
            1426980180 => 'Mar 21 11:23 pm<br/>Room: Humidity less than 800% and a low of 500% within the alert period.',
        ];

        $this->assertEquals($expected, $result);
    }

}
