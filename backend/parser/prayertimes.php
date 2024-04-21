<?php
header('Access-Control-Allow-Origin: *');

require_once 'sheets.php';
$sheets = new MasjidGoodleSheets();

$obj = (object)
[
    'Fajr_Start' => $sheets->getFajrDisplay(0),
    'Fajr_Adhan' => $sheets->getFajrDisplay(1),
    'Fajr_Iqama' => $sheets->getFajrDisplay(2),

    'Zuhr_Start' => $sheets->getZuhrDisplay(0),
    'Zuhr_Adhan' => $sheets->getZuhrDisplay(1),
    'Zuhr_Iqama' => $sheets->getZuhrDisplay(2),

    'Asr_Start' => $sheets->getAsrDisplay(0),
    'Asr_Adhan' => $sheets->getAsrDisplay(1),
    'Asr_Iqama' => $sheets->getAsrDisplay(2),

    'Maghrib_Start' => $sheets->getMaghribDisplay(0),
    'Maghrib_Adhan' => $sheets->getMaghribDisplay(1),
    'Maghrib_Iqama' => $sheets->getMaghribDisplay(2),

    'Isha_Start' => $sheets->getIshaDisplay(0),
    'Isha_Adhan' => $sheets->getIshaDisplay(1),
    'Isha_Iqama' => $sheets->getIshaDisplay(2),

    'Sunrise' => $sheets->getSunrise(false),
    'Zawal' => $sheets->getZawal(false),

    'Jumah_1_Start' => $sheets->getJumah1(false),
    'Jumah_1_Khutbah' => $sheets->getJumah1(true),

    'Jumah_2_Start' => $sheets->getJumah2(false),
    'Jumah_2_Khutbah' => $sheets->getJumah2(true),

    'Time_change' => $sheets->getTimeChangeString()[0],
    'Time_change_prayer_names' => $sheets->getTimeChangeString()[1],
];

echo json_encode($obj);

?>