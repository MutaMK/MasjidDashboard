<?php
    header('Access-Control-Allow-Origin: *');
    
    require_once 'sheets.php';
    $sheets = new MasjidGoodleSheets();

    $zhur_time = strtotime($sheets->getZuhr(0, false, false));
    $zawal_start = $zhur_time - (6*60); //subtract 6 minutes
    $zawal_end = $zhur_time - (1*60); //subtract 1 minutes


    if(time() >= $zawal_start && time() <= $zawal_end)
    {
        echo json_encode(true);
    }
    else
    {
        echo json_encode(false);
    }
?>