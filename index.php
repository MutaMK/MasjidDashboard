<html>

<head>
  <title>Masjid Huzaifah</title>
  <link rel="stylesheet" href="style/style.css?v=4">
</head>

<body>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
  <script src="/recurringActions.js?v=1"></script>
  <div class="main">
    <img class="masjid_logo" src="/images/Masjid_logo.png"/>
    <div id="clock"></div>
    <div id="date"></div>
    <hr class="title_line">
    <div class="time_table">
      <table class="mytable">
        <thead>
          <tr id="header_row">
            <th scope="col" class="salah_header">Salah</th>
            <th scope="col">Start Time</th>
            <th scope="col">Adhan</th>
            <th scope="col">Iqamah</th>
          </tr>
        </thead>
        <tr id="Fajr_row" class="table_row">
          <td scope="row" class="salah_header"> <img class="icon" src="/images/Fajr.svg"/> Fajr</td>
          <td id="Fajr_Start"></td>
          <td id="Fajr_Adhan"></td>
          <td id="Fajr_Iqama"></td>
        </tr>
        <tr id="Sunrise_row">
          <td scope="row" class="salah_header"> <img class="icon" src="/images/Sunrise.svg"/> Sunrise</td>
          <td colspan="3" id="Sunrise"></td>
        </tr>
        <tr id="Zawal_row" class="table_row">
          <td scope="row" class="salah_header"> <img class="icon" src="/images/Zawal.svg"/> Zawal</td>
          <td colspan="3" id="Zawal"></td>
        </tr>
        <tr id="Zuhr_row" class="table_row">
          <td scope="row" class="salah_header"> <img class="icon" src="/images/Zuhr.svg"/> Zuhr</td>
          <td id="Zuhr_Start"></td>
          <td id="Zuhr_Adhan"></td>
          <td id="Zuhr_Iqama"></td>
        </tr>
        <tr id="Asr_row" class="table_row">
          <td scope="row" class="salah_header"> <img class="icon" src="/images/Asr.svg"/> Asr</td>
          <td id="Asr_Start"></td>
          <td id="Asr_Adhan"></td>
          <td id="Asr_Iqama"></td>
        </tr>
        <tr id="Maghrib_row" class="table_row">
          <td scope="row" class="salah_header"> <img class="icon" src="/images/Maghrib.svg"/> Maghrib</td>
          <td id="Maghrib_Start"></td>
          <td id="Maghrib_Adhan"></td>
          <td id="Maghrib_Iqama"></td>
        </tr>
        <tr id="Isha_row" class="table_row">
          <td scope="row" class="salah_header"> <img class="icon" src="/images/Isha.svg"/> Isha</td>
          <td id="Isha_Start"></td>
          <td id="Isha_Adhan"></td>
          <td id="Isha_Iqama"></td>
        </tr>
      </table>
      <section id="jumah_times">
        <div id="jumah_1">
          <div id="jumah_titles">
          <img class="icon" src="/images/Jumah.svg"/> Jumah 1
          </div>
          <hr>
          <div>
            <p>
              <span>Start Time:</span>
              <span id="Jumah_1_Start"></span>  
            </p>
            <p>
              <span>Khutbah:</span>
              <span id="Jumah_1_Khutbah"></span>
            </p>
          </div>
        </div>
        <div id="jumah_2">
          <div id="jumah_titles">
          <img class="icon" src="/images/Jumah.svg"/> Jumah 2
          </div>
          <hr>
          <div>
            <p>
              <span>Start Time:</span>
              <span  id="Jumah_2_Start"></span>
            </p>
            <p>
              <span>Khutbah:</span>
              <span  id="Jumah_2_Khutbah"></span>
            </p>
          </div>
        </div>
      </section>
    </div>
    <div id="Time_change">
    </div>
  </div>
</body>

</html>