import time
import datetime
import re
import requests as remote_requests
import json
import threading
import sys
from enum import Enum
from obswebsocket import obsws, requests

class Salah(Enum):
    FAJR = 0
    SUNRISE = 1
    ZUHR = 2
    JUMAH1 = 3
    JUMAH2 = 4
    ASR = 5
    MAGHRIB = 6
    ISHA = 7

# add to this class as needed
class PrayerTimeKeys(Enum):
    FAJR_ADHAN = 'Fajr_Adhan'
    SUNRISE = 'Sunrise'
    ASR_IQAMA = 'Asr_Iqama'
    MAGHRIB_ADHAN = 'Maghrib_Adhan'
    MAGHRIB_IQAMA = 'Maghrib_Iqama'
    ISHA_IQAMA = 'Isha_Iqama'
    JUMAH1_START = 'Jumah_1_Start'

class ObsControl:
    def __init__(self):
        self.client = obsws("localhost", 4455)
        self.client.connect()

    def __del__(self):
        print("this is hanging on disconnect")
        self.client.disconnect()
        print("here")

    def StartStreaming(self):
        # Start streaming
        response = self.client.call(requests.StartStream())
        # wait until start streaming is done
        is_active = False
        while (not is_active):
            time.sleep(2)
            request = requests.GetStreamStatus()
            response = self.client.call(request)
            is_active = response.datain['outputActive']
        return

    def StopStreaming(self):
        # Stop streaming
        response = self.client.call(requests.StopStream())
        # wait until stop streaming is done
        is_active = True
        while (is_active):
            time.sleep(1)
            request = requests.GetStreamStatus()
            response = self.client.call(request)
            is_active = not response.datain['outputActive']
        return

    def StartRecording(self):
        # Start recording
        response = self.client.call(requests.StartRecord())
        # wait until start recording is done
        is_active = False
        while (not is_active):
            time.sleep(1)
            request = requests.GetRecordStatus()
            response = self.client.call(request)
            is_active = response.datain['outputActive']
        return

    def StopRecording(self):
        # Stop recording
        response = self.client.call(requests.StopRecord())
        # wait until stop recording is done
        is_active = True
        while (is_active):
            time.sleep(1)
            request = requests.GetRecordStatus()
            response = self.client.call(request)
            is_active = not response.datain['outputActive']
        return

class ObsWorkerThread:
    def __init__(self):
        self.obs_control = ObsControl()
        self.UpdateTimes()

    def UpdateTimes(self):
        prayer_time_url = 'https://dashboard.masjidhuzaifah.com/backend/parser/prayertimes.php'
        response = remote_requests.get(prayer_time_url)
        self.prayer_times = json.loads(response.text)

    def isTodayAsrLecture(self):
        today = datetime.datetime.today()
        return today == 100

    def isTodayFriday(self):
        today = datetime.datetime.today()
        return today == 4

    def SleepStreamSleep(self, key, start_ealier, stream_until):
        return self.SleepRecordOrStreamHelper(key, start_ealier, stream_until, True)

    def SleepRecordSleep(self, key, start_ealier, stream_until):
        return self.SleepRecordOrStreamHelper(key, start_ealier, stream_until, False)

    def SleepRecordOrStreamHelper(self, key, start_ealier, stream_until, should_stream):
        current_time = datetime.datetime.now()
        prayer_time = datetime.datetime.strptime(self.prayer_times[key.value], "%I:%M %p")

        sleep_seconds = ((prayer_time.hour +  prayer_time.minute / 60 ) - (current_time.hour + current_time.minute / 60)) * 3600
        str_rec_print = "Recording: "
        if (should_stream):
            str_rec_print = "Streaming: "
        print(str_rec_print + "about to sleep for " + str(sleep_seconds/60) + ", for prayer " + key.value + ", for length " + str(stream_until/60) + ",  current time is " + current_time.strftime("%H:%M:%S"))
        if (sleep_seconds > 0):
            sleep_seconds = max(sleep_seconds - start_ealier, 0)
            time.sleep(sleep_seconds)
            if (should_stream):
                self.obs_control.StartStreaming()
            else:
                self.obs_control.StartRecording()
            time.sleep(stream_until)
            if (should_stream):
                self.obs_control.StopStreaming()
            else:
                self.obs_control.StopRecording()
            return True
        return False

    def StreamingThread(self):
        current_salah = Salah.FAJR
        while(True):
            match current_salah:
                case Salah.FAJR:
                    print("Streaming: Executing case Salah.FAJR")
                    steam_until = 20 * 60
                    before_athan = 60 * 2
                    self.SleepStreamSleep(PrayerTimeKeys.FAJR_ADHAN, before_athan, steam_until)

                    if self.isTodayFriday():
                        current_salah = Salah.JUMAH1
                    elif self.isTodayAsrLecture():
                        current_salah = Salah.ASR
                    else:
                        current_salah = Salah.MAGHRIB

                case Salah.SUNRISE | Salah.ZUHR:
                    print("Streaming: Executing case Salah.SUNRISE | Salah.ZUHR")
                    if self.isTodayFriday():
                        current_salah = Salah.JUMAH1
                    elif self.isTodayAsrLecture():
                        current_salah = Salah.ASR
                    else:
                        current_salah = Salah.MAGHRIB

                case Salah.JUMAH1:
                    print("Streaming: Executing case Salah.JUMAH1")
                    steam_until = 40 * 60
                    before_iqama = 60 * 2
                    self.SleepStreamSleep(PrayerTimeKeys.JUMAH1_START, before_iqama, steam_until)
                    
                    if self.isTodayAsrLecture():
                        current_salah = Salah.ASR
                    else:
                        current_salah = Salah.MAGHRIB

                case Salah.JUMAH2:
                    print("Streaming: Executing case Salah.JUMAH2")
                    if self.isTodayAsrLecture():
                        current_salah = Salah.ASR
                    else:
                        current_salah = Salah.MAGHRIB

                case Salah.ASR:
                    print("Streaming: Executing case Salah.ASR")
                    current_salah = Salah.MAGHRIB
                    if self.isTodayAsrLecture():
                        asr_time = datetime.datetime.strptime(self.prayer_times[PrayerTimeKeys.ASR_IQAMA.value], "%I:%M %p")
                        isha_time = datetime.datetime.strptime(self.prayer_times[PrayerTimeKeys.ISHA_IQAMA.value], "%I:%M %p") 
                        steam_until = ((isha_time.hour +  isha_time.minute / 60 ) - (asr_time.hour + asr_time.minute / 60)) * 3600
                        steam_until += 120 * 60

                        before_iqama = 0
                        if self.SleepStreamSleep(PrayerTimeKeys.ASR_IQAMA, before_iqama, steam_until) == True:
                            current_salah = Salah.ISHA

                case Salah.MAGHRIB:
                    print("Streaming: Executing case Salah.MAGHRIB")
                    maghrib_time = datetime.datetime.strptime(self.prayer_times[PrayerTimeKeys.MAGHRIB_ADHAN.value], "%I:%M %p") 
                    isha_time = datetime.datetime.strptime(self.prayer_times[PrayerTimeKeys.ISHA_IQAMA.value], "%I:%M %p") 
                    steam_until = ((isha_time.hour +  isha_time.minute / 60 ) - (maghrib_time.hour + maghrib_time.minute / 60)) * 3600
                    steam_until += 120 * 60
            
                    before_iqama = 30 * 60
                    self.SleepStreamSleep(PrayerTimeKeys.MAGHRIB_ADHAN, before_iqama, steam_until)
                    current_salah = Salah.ISHA

                case Salah.ISHA:
                    print("Streaming: Executing case Salah.ISHA")
                    steam_until = 120 * 60
                    before_iqama = 60 * 2
                    self.SleepStreamSleep(PrayerTimeKeys.ISHA_IQAMA, before_iqama, steam_until)

                    # if isha ended past midnight at a max of 2 am
                    # then update and go to fajr, otherwise sleep
                    # until 12:02 am then update and go to fajr
                    current_time = datetime.datetime.now() #                 current_time = time.localtime()     
                    if (current_time.hour < 2):
                        self.UpdateTimes()
                        current_salah = Salah.FAJR
                    else:
                        sleep_seconds = ((24) - (current_time.hour + current_time.minute / 60)) * 3600
                        print("Streaming: about to sleep for " + str(sleep_seconds/60) + ", for until 12 am, current time is " + current_time.strftime("%H:%M:%S"))
                        time.sleep(sleep_seconds + 60 * 2)
                        self.UpdateTimes()
                        current_salah = Salah.FAJR
            
                case _:
                    print("BUG impossible enum value for streamer")
                    break

    def RecordingThread(self):
        current_salah = Salah.FAJR
        while(True):
            match current_salah:
                case Salah.FAJR | Salah.SUNRISE | Salah.ZUHR | Salah.JUMAH1 | Salah.JUMAH2:
                    print("Recording: Executing case Salah.FAJR | Salah.SUNRISE | Salah.ZUHR | Salah.JUMAH1 | Salah.JUMAH2")
                    if self.isTodayAsrLecture():
                        current_salah = Salah.ASR
                    else:
                        current_salah = Salah.MAGHRIB

                case Salah.ASR:
                    print("Recording: Executing case Salah.ASR")
                    if self.isTodayAsrLecture():
                        record_until = 40 * 60
                        before_iqama = 0
                        self.SleepRecordSleep(PrayerTimeKeys.ASR_IQAMA, before_iqama, record_until)

                    current_salah = Salah.MAGHRIB

                case Salah.MAGHRIB:
                    print("Recording: Executing case Salah.MAGHRIB")
                    record_until = 15 * 60
                    before_iqama = 60 * 2
                    self.SleepRecordSleep(PrayerTimeKeys.MAGHRIB_IQAMA, before_iqama, record_until)
                    current_salah = Salah.ISHA

                case Salah.ISHA:
                    print("Recording: Executing case Salah.ISHA")
                    record_until = 120 * 60
                    before_iqama = 60 * 2
                    self.SleepStreamSleep(PrayerTimeKeys.ISHA_IQAMA, before_iqama, record_until)

                    # if isha ended past midnight at a max of 2 am
                    # then update and go to fajr, otherwise sleep
                    # until 12:02 am then update and go to fajr
                    current_time = datetime.datetime.now()
                    if (current_time.hour < 2):
                        self.UpdateTimes()
                        current_salah = Salah.FAJR
                    else:
                        sleep_seconds = ((24) - (current_time.hour + current_time.minute / 60)) * 3600
                        print("Recording: about to sleep for " + str(sleep_seconds/60) + ", for until 12 am, for recording thread, current time is " + current_time.strftime("%H:%M:%S"))
                        time.sleep(sleep_seconds + 60 * 2)
                        self.UpdateTimes()
                        current_salah = Salah.FAJR

                case _:
                    print("BUG impossible enum value for recorder")
                    break

if __name__ == '__main__':
    obs_control = ObsControl()

    # Create instances of MyClass
    obj1 = ObsWorkerThread()
    obj2 = ObsWorkerThread()

    # Create threads and set target to the member function directly
    thread1 = threading.Thread(target=obj1.StreamingThread)
    thread2 = threading.Thread(target=obj2.RecordingThread)

    # Start threads
    thread1.start()
    thread2.start()

    # Wait for threads to finish
    thread1.join()
    thread2.join()

    sys.exit()
