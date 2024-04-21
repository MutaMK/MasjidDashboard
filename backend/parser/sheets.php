<?php

date_default_timezone_set('America/Toronto');

class SheetsFileNotFoundException extends Exception {}

class MasjidGoodleSheets
{
    public function __construct()
    {
        $file_raw_data = file_get_contents(__DIR__ . '/../data/GoogleSheetPulled.json');
        if (!$file_raw_data)
        {
            throw new SheetsFileNotFoundException();
        }
        $all_rows = json_decode($file_raw_data);

        
        $this->header_row = array_map('strtolower', $all_rows[0]);
        
        $this->today_row = self::getSheetRow($all_rows, "today");
        $this->tomorrow_row = self::getSheetRow($all_rows, "tmrw");
        $this->friday_row = self::getSheetRow($all_rows, "friday");
    }

    private function getSheetRow($sheet_values, $date_select)
    {
        // $header_row = $sheet_values[0]; 
        // $header_row = array_map('strtolower', $header_row);
        $date_cell = array_search('date', $this->header_row);
        $date = new DateTime();

        if ($date_select == "tmrw")
        {
            $date->modify('+1 day');
        }
        else if ($date_select == "friday" && $date->format('N') != 5)
        {
            $date->modify('next friday'); // only if today is not friday
        }
        
        for ($i = 0; $i < count($sheet_values); $i++)
        {
            if ($sheet_values[$i][$date_cell] == date_format($date, 'm-d'))
            {
                return $sheet_values[$i];
            }
        }
    }

    // selector -> 0 is start time, 1 is adhan time, 2 is iqama
    private function calculatePrayerTime($selector, $tomorrow, $format_time, $start_cell, $adhan_cell, $iqama_cell)
    {
        $row_to_use = $this->today_row;

        if ($tomorrow)
        {
            $row_to_use = $this->tomorrow_row;
        }

        $retval = $row_to_use[array_search($start_cell, $this->header_row)];

        if($selector == 0) $retval = $row_to_use[array_search($start_cell, $this->header_row)];
        else if($selector == 1) $retval = $row_to_use[array_search($adhan_cell, $this->header_row)];
        else if($selector == 2) $retval = $row_to_use[array_search($iqama_cell, $this->header_row)];
        else return "Error: bad selector value";

    
        //formatting time from 24hr to 12 hr
        if ($format_time)
        {
            $retval  = date("g:i a", strtotime($retval));
        }

        return $retval;
    }

    function fiveMinutesAfterIqama($prayer)
    {
        $prayers = ['getFajr', 'getZuhr', 'getAsr' , 'getMaghrib', 'getIsha'];
        $name_to_func = (object)
        [
            'fajr' => 0,
            'zuhr' => 1,
            'asr' => 2,
            'maghrib' => 3,
            'isha' => 4,
        ];
        $index = $name_to_func->{$prayer};
        $five_min_iqama_time = strtotime('+ ' . 5 . ' minutes', strtotime($this->{$prayers[$index]}(2, false, false)));

        if (time() >= $five_min_iqama_time)
        {
            return true;
        }
        
        return false;
    }

    // --------Api for usage-----------
    // selector -> 0 is start time, 1 is adhan time, 2 is iqama
    function getFajr($selector, $tomorrow, $format_time = true)
    {
        return $this->calculatePrayerTime($selector, $tomorrow, $format_time, 'fajr-start', 'fajr-azan', 'fajr-iqama');
    }
    
    function getZuhr($selector, $tomorrow, $format_time = true)
    {
        return $this->calculatePrayerTime($selector, $tomorrow, $format_time, 'zuhr-start', 'zuhr-azan', 'zuhr-iqama');
    }
    
    function getAsr($selector, $tomorrow, $format_time = true)
    {
        return $this->calculatePrayerTime($selector, $tomorrow, $format_time, 'asr-start', 'asr-azan', 'asr-iqama');
    }
    
    function getMaghrib($selector, $tomorrow, $format_time = true)
    {
        return $this->calculatePrayerTime($selector, $tomorrow, $format_time, 'maghrib-start', 'maghrib-azan', 'maghrib-iqama');
    }
    
    function getIsha($selector, $tomorrow, $format_time = true)
    {   
        return $this->calculatePrayerTime($selector, $tomorrow, $format_time, 'isha-start', 'isha-azan', 'isha-iqama');
    }
    
    // TDODO: better way to do these function isntead of repeatig the same parameter 3 times
    function getSunrise($tomorrow)
    {
        return $this->calculatePrayerTime(0, $tomorrow, true, 'sunrise', 'sunrise', 'sunrise');
    }
    
    function getDuha($tomorrow)
    {
        return $this->calculatePrayerTime(0, $tomorrow, true, 'duha', 'duha', 'duha');
    }
    
    function getZawal($tomorrow)
    {
        
        return $this->calculatePrayerTime(0, $tomorrow, false, 'zawal', 'zawal', 'zawal');
    }

    //these set of functions will return tomorrows time 5 minutes after the iqama
    function getFajrDisplay($selector, $format_time = true)
    {
        if ($this->fiveMinutesAfterIqama("fajr"))
        {
            return $this->calculatePrayerTime($selector, true, $format_time, 'fajr-start', 'fajr-azan', 'fajr-iqama');
        }
        return $this->calculatePrayerTime($selector, false, $format_time, 'fajr-start', 'fajr-azan', 'fajr-iqama');
    }
    
    function getZuhrDisplay($selector, $format_time = true)
    {
        if ($this->fiveMinutesAfterIqama("zuhr"))
        {
            return $this->calculatePrayerTime($selector, true, $format_time, 'zuhr-start', 'zuhr-azan', 'zuhr-iqama');
        }
        return $this->calculatePrayerTime($selector, false, $format_time, 'zuhr-start', 'zuhr-azan', 'zuhr-iqama');
    }
    
    function getAsrDisplay($selector, $format_time = true)
    {
        if ($this->fiveMinutesAfterIqama("asr"))
        {
            return $this->calculatePrayerTime($selector, true, $format_time, 'asr-start', 'asr-azan', 'asr-iqama');
        }
        return $this->calculatePrayerTime($selector, false, $format_time, 'asr-start', 'asr-azan', 'asr-iqama');
    }
    
    function getMaghribDisplay($selector, $format_time = true)
    {
        if ($this->fiveMinutesAfterIqama("maghrib"))
        {
            return $this->calculatePrayerTime($selector, true, $format_time, 'maghrib-start', 'maghrib-azan', 'maghrib-iqama');
        }
        return $this->calculatePrayerTime($selector, false, $format_time, 'maghrib-start', 'maghrib-azan', 'maghrib-iqama');
    }
    
    function getIshaDisplay($selector, $format_time = true)
    {   
        if ($this->fiveMinutesAfterIqama("isha"))
        {
            return $this->calculatePrayerTime($selector, true, $format_time, 'isha-start', 'isha-azan', 'isha-iqama');
        }
        return $this->calculatePrayerTime($selector, false, $format_time, 'isha-start', 'isha-azan', 'isha-iqama');
    }
    
    //khutbah = true -> get khutbah time, false -> get ADHAN
    function getJumah1($khutbah)
    {
        if ($khutbah)
        {
            $retval = $this->friday_row[array_search('khutbah-1', $this->header_row)];
        }
        else
        {
            $retval = $this->friday_row[array_search('jumah-azan-1', $this->header_row)];

        }

        $retval  = date("g:i a", strtotime($retval));
        return $retval;
        // return $this->calculatePrayerTime($khutbah, false, true, 'jumah-azan-1', 'khutbah-1', 'khutbah-1');;
    }
    
    function getJumah2($khutbah)
    {
        if ($khutbah)
        {
            $retval = $this->friday_row[array_search('khutbah-2', $this->header_row)];
        }
        else
        {
            $retval = $this->friday_row[array_search('jumah-azan-2', $this->header_row)];

        }

        $retval  = date("g:i a", strtotime($retval));
        return $retval;
        // return $this->calculatePrayerTime($khutbah, false, true, 'jumah-azan-2', 'khutbah-2', 'khutbah-2');
    }

    function getTimeChangeString()
    {
        $prayers = ['getFajr', 'getZuhr', 'getAsr' ,'getIsha'];
        $prayersNames = ['Fajr', 'Zuhr', 'Asr', 'Isha'];
        $prayersNamesRows = ['Fajr_row', 'Zuhr_row', 'Asr_row', 'Isha_row'];
        $should_print = false;
        $outputString = '';
        $outputArray = array();

        for ($i = 0; $i < count($prayers); $i++)
        {
            if(strtotime($this->{$prayers[$i]}(2, false, false)) != strtotime($this->{$prayers[$i]}(2, true, false)))
            {
                array_push($outputArray,$prayersNamesRows[$i]);
                if(!$should_print)
                {
                    $date = new DateTime();
                    $date->modify('+1 day');
                    $outputString = "From tomorrow, " . $date->format('D, M d') . ", ";
                    $should_print = true;
                }
                
                $outputString .= " " . $prayersNames[$i] . " is at " . ($this->{$prayers[$i]}(2, true, true)) . ", ";
            }
        }

        if($should_print)
        {
            return array(substr($outputString, 0, -2), $outputArray);
        }
        return array("", $outputArray);
    }

    private $header_row;
    private $today_row;
    private $tomorrow_row;
    private $friday_row;
}