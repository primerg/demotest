<?php namespace IO\Utilities\Alert;

use IO\Utilities\Alert\Counter;

class CounterDuration extends Counter
{
    public function count() 
    {
        // print strtotime(date('2015-03-23 00:05:00'));die();
        $errorRedCtr = 0;
        $errorYelCtr = 0;

        $duration = $this->rules->duration / 5; // since our call is every 5 minutes

        $batchError = [];
        $min = 0;
        $max = 0;
        $color = 'Yellow';

        $batchErrorCtr = 0;

        $lastDate = ''; // This is or day check
        $lastLoggedError = ''; // This is for keycheck
        $addToLog = true;

        foreach ($this->readings as $readingDate => $value) {
            $readingValue = $value[1];
            $currentDate = date('Y-m-d', $readingDate);

            // if ($currentDate != $lastDate) {
            //     print "$currentDate = $lastDate - $batchErrorCtr\n";
            // }

            if ($isGood = $this->isGood($readingValue)) {
                if ($batchErrorCtr) {
                    $this->addLog($batchError[0], $batchError[1], $color, ['min' => $min, 'max' => $max]);
                }

                // Reset
                $min = $max = $batchErrorCtr = 0;
                $batchError = [];
                $color = 'Yellow';

                $lastDate = $currentDate;
                continue;
            }

            if ($currentDate != $lastDate) {
                if ($batchErrorCtr) {
                    $this->addLog($batchError[0], $batchError[1], $color, ['min' => $min, 'max' => $max]);
                }

                // Reset
                $min = $max = $batchErrorCtr = 0;
                $batchError = [];
                $color = 'Yellow';
            }

            $batchErrorCtr++;

            // print $batchErrorCtr . " - $readingValue - $readingDate\n";

            if ($batchErrorCtr == 1) {
                $batchError = [$readingValue, $readingDate];
                $min = $max = $readingValue;
            } else {
                if ($readingValue >= $max) {
                    $max = $readingValue;
                } else if ($readingValue < $min) {
                    $min = $readingValue;
                }
            }

            if ($batchErrorCtr > $duration) {
                $color = 'Red';
            }
            
            $lastDate = $currentDate;
        }

        if ($batchErrorCtr) {
            $this->addLog($batchError[0], $batchError[1], $color, ['min' => $min, 'max' => $max]);
        }

        // print_r($this->log);
        // die();
    }

    // Green
    public function isGood($value) {
        if ($value < $this->rules->above && $value >= $this->rules->below) {
            return true;
        }

        return false;
    }
}