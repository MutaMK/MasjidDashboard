<?php
    header('Access-Control-Allow-Origin: *');

    require_once 'sheets.php';
    $sheets = new MasjidGoodleSheets();

    $file_raw_data = file_get_contents(__DIR__ . '/../data/GoogleSheetPulledHijri.json');
    if (!$file_raw_data)
    {
        throw new SheetsFileNotFoundException();
    }
    $all_rows = json_decode($file_raw_data);

    $maghrib_time = $sheets->getMaghrib(2, false, false);
    if(time() > strtotime($maghrib_time))
    {
        echo json_encode($all_rows->Hijri_Date_Maghrib, JSON_UNESCAPED_UNICODE );
    }
    else
    {
        echo json_encode($all_rows->Hijri_Date, JSON_UNESCAPED_UNICODE );
    }
?>