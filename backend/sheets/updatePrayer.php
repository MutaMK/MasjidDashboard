<?php
    require __DIR__ . '/../../vendor/autoload.php';

    $client = new \Google_Client();
    $client->setApplicationName('Masjid Huzifia sheets');
    $client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
    $client->setAccessType('offline');
    $client->setAuthConfig(__Dir__ . '/credentials.json');

    $service = new Google_Service_Sheets($client);
    $spreadsheetId = "TODO : Put yout google sheets id here";

    $SHEET_NAME_IN_FILE = "'ProductionPrayerTimes'";

    // This is default, but include anyways in case we want to change
    $params['valueRenderOption'] = 'FORMATTED_VALUE';

    $response = $service->spreadsheets_values->get($spreadsheetId, $SHEET_NAME_IN_FILE, $params);
    $all_rows = $response->getValues();
    if (empty($all_rows)) {
        echo "Error: could not get data from google sheets for {$SHEET_NAME_IN_FILE} \n";
        return;
    }

    $json = json_encode($all_rows);

    //write json to file
    if (file_put_contents(__DIR__ . "/../data/GoogleSheetPulled.json", $json))
        echo "Prayer Times updated created successfully!\n";
    else
        echo "Failed to update. Error Writing to the json file\n";    
?>