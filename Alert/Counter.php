<?php namespace IO\Utilities\Alert;

class Counter
{
    protected $rules;
    protected $readings;

    protected $log;

    // What is the above, below and middle
    public $below;
    public $above;

    public $toleranceType; // Normal or Duration or time
    
    // If tolerance is enabled, then we need this settings
    public $toleranceAbove;
    public $toleranceBelow;

    public $stat = [
        'red' => 0,
        'yellow' => 0
    ];

    public function setConfig($config)
    {
        $this->rules = (object)$config;
        return;

        // Example
        $this->rules = [
            'device' => 'test',
            'period' => ['2015-03-21 00:00:00', '2015-03-21 59:59:59'],
            'preset' => 'Ministry',
            'below'  => '800',
            'above'  => '1000',
            'duration' => 10, // min
        ];
    }

    public function __construct($readings, $rules = null)
    {
        if ($rules) {
            $this->rules = (object)$rules;    
        }
        
        $this->readings = $readings;
    }

    public function log()
    {
        return $this->log;
    }

    public function addLog($readingValue, $readingDate, $color = 'Red', $params = []) {
        $this->log[$readingDate] = [
            'reading' => [$readingValue, $readingDate],
            'color'    => $color,
            'formattedDate' => date('Y-m-d H:i:s' , $readingDate),
            'range' => [
                'min' => $readingValue,
                'max' => $readingValue,
            ]
        ];

        if (isset($params['min'])) {
            $this->log[$readingDate]['range']['min'] = $params['min'];
        }

        if (isset($params['max'])) {
            $this->log[$readingDate]['range']['max'] = $params['max'];
        }

        if ($color == 'Red') {
            $this->stat['red']++;
        } else if ($color == 'Yellow') {
            $this->stat['yellow']++;
        }
    }
}