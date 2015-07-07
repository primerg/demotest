<?php namespace IO\Utilities\Alert;

use IO\Utilities\Alert\Counter;

class CounterDefault extends Counter
{
    public function count() 
    {
        $errorRedCtr = 0;

        $lastDate = ''; // This is or day check
        $lastReading = ''; // This is for keycheck
        $addToLog = true;

        foreach ($this->readings as $readingDate => $value) {
            $readingValue = $value[1];
            $currentDate = date('Y-m-d', $readingDate);

            if ($currentDate != $lastDate){
                $addToLog = true;
            }

            if ($this->isGood($readingValue)) {
                // It is green so the next error should be logged
                $addToLog = true;
            } else {
                // Only add if the last one was not an error
                if ($addToLog) {
                    $this->addLog($readingValue, $readingDate, 'Red');

                    // This indicates that we already added this and the next error shouldn't
                    $addToLog = false;
                    $errorRedCtr++;
                    $lastReading = $readingDate;
                } else if ($lastReading != '') {
                    if ($readingValue >= $this->log[$lastReading]['range']['max']) {
                        $this->log[$lastReading]['range']['max'] = $readingValue;
                    } else if ($readingValue < $this->log[$lastReading]['range']['min']) {
                        $this->log[$lastReading]['range']['min'] = $readingValue;
                    }
                }
            }            


            $lastDate = $currentDate;
        }


        $this->stat['red'] = $errorRedCtr;
        $this->stat['yellow'] = 0;
    }

    // Green
    public function isGood($value) {
        if ($value < $this->rules->above && $value >= $this->rules->below) {
            return true;
        }

        return false;
    }
}