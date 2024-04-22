<?php
    require __DIR__ . '/../../vendor/autoload.php';

    $client = new \Google_Client();
    $client->setApplicationName('Masjid Huzifia sheets');
    $client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
    $client->setAccessType('offline');
    // TODO : uncomment the below line once you have a real google sheet and real authentication 
    // $client->setAuthConfig(__Dir__ . '/credentials.json');

    // TODO: You can also use the api Key method instead. The below is just an example.
    $apiKey = 'AIzaSyCC7JOnbjZY-3yFC9HJ8gmjkb7flGMGElg';
    $client->setDeveloperKey($apiKey);

    $service = new Google_Service_Sheets($client);
    // TODO : Put your google sheets id here
    $spreadsheetId = "1RKmh4Cau3xclHTQQdT8t6jPkoEOXil8pZCmEXGDfGQg";

    $SHEET_NAME_IN_FILE = "hijriCal";

    // This is default, but include anyways in case we want to change
    $params['valueRenderOption'] = 'FORMATTED_VALUE';

    $response = $service->spreadsheets_values->get($spreadsheetId, $SHEET_NAME_IN_FILE, $params);
    $all_rows = $response->getValues();
    if (empty($all_rows))
    {
        echo "Error: could not get data from google sheets for {$SHEET_NAME_IN_FILE} \n";
        return;
    }

    $obj = (object)
    [
        'Hijri_Date' => $all_rows[3][6],
        'Hijri_Date_Maghrib' => $all_rows[7][6],
    ];

    $json = json_encode($obj, JSON_UNESCAPED_UNICODE);

    //write json to file
    if (file_put_contents(__DIR__ . "/../data/GoogleSheetPulledHijri.json", $json))
        echo "Updated Hijri Dates.\n";
    else
        echo "Failed to updated Hijri Dates.\n";
?>