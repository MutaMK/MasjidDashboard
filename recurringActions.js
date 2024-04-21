$(document).ready(function () {
    UpdateClock();
    UpdateDate();
    UpdatePrayerTimes();
    ColorCurrentTime();

    setInterval(UpdateClock, 1000); //every second
    setInterval(EveryMinute, 60000); //every minute
});

function EveryMinute()
{
    var date = new Date();
    if (date.getHours() === 0 && date.getMinutes() === 0)  // Check if it is 12 am
    {
        UpdateDateEvery24();
    }

    UpdatePrayerTimes();
    ColorCurrentTime();
    UpdateDate();
}

function UpdateDate() {
    var date = new Date();
    var greg_date = date.toLocaleDateString('en-us', { weekday:"long", month:"long", day:"numeric"});

    $.ajax({
        url: '/backend/parser/getHijri.php',
        success: function (data) {
            var hijri_date = JSON.parse(data);
            var date_string = greg_date + " | " + hijri_date;
            document.getElementById('date').innerHTML = date_string;
        },
    });
}

function UpdateClock() {
    var date = new Date();
    var options = {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: true, // Force 12-hour format
    };
    var date_string = date.toLocaleTimeString([], options);
    // remove leading 0 so 02 -> 2 for example
    date_string = parseInt(date_string.substring(0, 2)).toString() + date_string.substring(2);
    document.getElementById('clock').innerHTML = date_string;
}

function ColorCurrentTime() {
    $.ajax({
        url: '/backend/parser/GetCurrentPrayer.php',
        success: function (data) {
            let row_names = JSON.parse(data)
            const gray_element = document.getElementById(row_names.Last_prayer);
            gray_element.classList.remove("table-dark-highlight");

            const green_element = document.getElementById(row_names.Current_prayer);
            green_element.classList.add("table-dark-highlight");
        },
    });

    $.ajax({
        url: '/backend/parser/isZawalTime.php',
        success: function (data) {
            let is_zawal_time = JSON.parse(data)
            if (is_zawal_time)
            {
                document.getElementById('Zawal_row').style.color = '#FF0202'; 
            }
            else
            {
                document.getElementById('Zawal_row').style.color = '#ffffff';
            }
        },
    });
}

function UpdateDateEvery24() {
    $.ajax({
        url: '/backend/sheets/updateHijri.php',
        success: function () {
            UpdateDate();
        },
    });
}

function UpdatePrayerTimes() {
    $.ajax({
        url: '/backend/parser/prayertimes.php',
        success: function (data) {
            let prayer_times = JSON.parse(data)
            document.getElementById('Fajr_Start').innerHTML = prayer_times.Fajr_Start
            document.getElementById('Fajr_Adhan').innerHTML = prayer_times.Fajr_Adhan
            document.getElementById('Fajr_Iqama').innerHTML = prayer_times.Fajr_Iqama

            document.getElementById('Zuhr_Start').innerHTML = prayer_times.Zuhr_Start
            document.getElementById('Zuhr_Adhan').innerHTML = prayer_times.Zuhr_Adhan
            document.getElementById('Zuhr_Iqama').innerHTML = prayer_times.Zuhr_Iqama

            document.getElementById('Asr_Start').innerHTML = prayer_times.Asr_Start
            document.getElementById('Asr_Adhan').innerHTML = prayer_times.Asr_Adhan
            document.getElementById('Asr_Iqama').innerHTML = prayer_times.Asr_Iqama

            document.getElementById('Maghrib_Start').innerHTML = prayer_times.Maghrib_Start
            document.getElementById('Maghrib_Adhan').innerHTML = prayer_times.Maghrib_Adhan
            document.getElementById('Maghrib_Iqama').innerHTML = prayer_times.Maghrib_Iqama

            document.getElementById('Isha_Start').innerHTML = prayer_times.Isha_Start
            document.getElementById('Isha_Adhan').innerHTML = prayer_times.Isha_Adhan
            document.getElementById('Isha_Iqama').innerHTML = prayer_times.Isha_Iqama

            document.getElementById('Sunrise').innerHTML = prayer_times.Sunrise
            document.getElementById('Zawal').innerHTML = prayer_times.Zawal
            document.getElementById('Time_change').innerHTML = prayer_times.Time_change

            document.getElementById('Jumah_1_Start').innerHTML = prayer_times.Jumah_1_Start
            document.getElementById('Jumah_1_Khutbah').innerHTML = prayer_times.Jumah_1_Khutbah

            document.getElementById('Jumah_2_Start').innerHTML = prayer_times.Jumah_2_Start
            document.getElementById('Jumah_2_Khutbah').innerHTML = prayer_times.Jumah_2_Khutbah

            document.getElementById('Time_change').innerHTML = prayer_times.Time_change

            // set all row colors to white, and if time change then set to the redish color
            const prayer_rows_that_change_color = ['Fajr_row', 'Zuhr_row', 'Asr_row', 'Isha_row'];
            prayer_rows_that_change_color.forEach(element => {
                document.getElementById(element).style.color = '#ffffff';
            });
            prayer_times.Time_change_prayer_names.forEach(element => {
                document.getElementById(element).style.color = '#FF0202';
            });
        },
    });
}