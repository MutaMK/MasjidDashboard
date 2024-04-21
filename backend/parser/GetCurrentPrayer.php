<?php
header('Access-Control-Allow-Origin: *');

require_once 'sheets.php';

function GetCurrentPrayer()
{
  $sheets = new MasjidGoodleSheets();

  $starttime = [
    strtotime($sheets->getFajr(0, false, false)),  strtotime($sheets->getSunrise(0, false, false)),
    strtotime($sheets->getZuhr(0, false, false)), strtotime($sheets->getAsr(0, false, false)),
    strtotime($sheets->getMaghrib(0, false, false)), strtotime($sheets->getIsha(0, false, false))
  ];
  $prayer_row_names = ['Fajr_row', 'Sunrise_row', 'Zuhr_row', 'Asr_row', 'Maghrib_row', 'Isha_row'];

  $date_time = new DateTime();
  if ($date_time->format('N') == 5) // if today is friday
  {
    $starttime = [
      strtotime($sheets->getFajr(0, false, false)), strtotime($sheets->getSunrise(0, false, false)),
      strtotime($sheets->getJumah1(false)), strtotime($sheets->getJumah2(false)),
      strtotime($sheets->getAsr(0, false, false)), strtotime($sheets->getMaghrib(0, false, false)),
      strtotime($sheets->getIsha(0, false, false))
    ];
    $prayer_row_names = ['Fajr_row', 'Sunrise_row', 'jumah_1', 'jumah_2', 'Asr_row', 'Maghrib_row', 'Isha_row'];
  }

  $time = time();

  $obj = (object)
  [
    'Current_prayer' => $prayer_row_names[0],
    'Last_prayer' => $prayer_row_names[0],
  ];

  // should make this code look better. Remove the 'Last_prayer' part?
  for ($i = 0; $i < count($starttime); $i++)
  {
    $next_i = $i + 1;
    $prev_i = $i - 1;
    if ($i == 0)
    {
      $prev_i = count($starttime) - 1;
    }
    elseif($i == count($starttime) - 1)
    {
      $next_i = 0;
      // if less than fajr, return isha OR bigger than isha return isha
      // This is because this code only considers time and not date.
      // This if is not really needed
      if ($time >= $starttime[$i] || $time < $starttime[$next_i])
      {
        $obj->{'Current_prayer'} = $prayer_row_names[$i];
        $obj->{'Last_prayer'} = $prayer_row_names[$prev_i];
        break;
      }
    }

    if ($time >= $starttime[$i] && $time < $starttime[$next_i])
    {
      $obj->{'Current_prayer'} = $prayer_row_names[$i];
      $obj->{'Last_prayer'} = $prayer_row_names[$prev_i];
      break;
    }
  }

  return json_encode($obj);
}

echo GetCurrentPrayer();

?>