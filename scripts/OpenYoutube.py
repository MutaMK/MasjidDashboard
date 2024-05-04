import webbrowser
import os
import obspython as obs
import time
from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.chrome.webdriver import WebDriver
from selenium.webdriver.chromium.options import ChromiumOptions as Options
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC

def ConnectAndStream(data):
    global driver
    global service

    if data == obs.OBS_FRONTEND_EVENT_STREAMING_STARTING:
        service = Service(executable_path='C:/Users/TODO/Desktop/chromedriver.exe')
        driver_options = Options()
        driver_options.add_argument('--user-data-dir=C:/Users/TODO/AppData/Local/Google/Chrome/User Data')
        driver = webdriver.Chrome(options=driver_options, service=service)

        print("Recording started - here")
        driver.get("https://studio.youtube.com/channel/TODO/livestreaming/dashboard")
        # Wait for the page to load
        WebDriverWait(driver, 60).until(
            EC.presence_of_element_located((By.XPATH, "/html/body/ytcp-app/ytls-live-streaming-section/ytls-core-app/div/div[2]/div/ytls-live-dashboard-page-renderer/div[1]/div[1]/ytls-live-control-room-renderer/div[1]/ytls-widget-section/ytls-stream-settings-widget-renderer/div[2]/ytls-metadata-collection-renderer[1]/div[2]/div/ytls-metadata-control-renderer[3]/div/ytls-ingestion-settings-item-renderer/div[2]/tp-yt-paper-input/tp-yt-paper-input-container/div[2]/div/iron-input/input"))
        )
        # sleep for 5 seconds just in case not done loading
        time.sleep(5)

    elif data == obs.OBS_FRONTEND_EVENT_STREAMING_STOPPED:
        print("Recording stopped - there")
        driver.close()

        # open youtube again to reset the youtube streaming title
        time.sleep(5)
        service = Service(executable_path='C:/Users/TODO/Desktop/chromedriver.exe')
        driver_options = Options()
        driver_options.add_argument('--user-data-dir=C:/Users/TODO/AppData/Local/Google/Chrome/User Data')
        driver = webdriver.Chrome(options=driver_options, service=service)

        print("Recording started - here")
        driver.get("https://studio.youtube.com/channel/TODO/livestreaming/dashboard")
        # Wait for the page to load
        WebDriverWait(driver, 60).until(
            EC.presence_of_element_located((By.XPATH, "/html/body/ytcp-app/ytls-live-streaming-section/ytls-core-app/div/div[2]/div/ytls-live-dashboard-page-renderer/div[1]/div[1]/ytls-live-control-room-renderer/div[1]/ytls-widget-section/ytls-stream-settings-widget-renderer/div[2]/ytls-metadata-collection-renderer[1]/div[2]/div/ytls-metadata-control-renderer[3]/div/ytls-ingestion-settings-item-renderer/div[2]/tp-yt-paper-input/tp-yt-paper-input-container/div[2]/div/iron-input/input"))
        )
        # sleep for 5 seconds just in case not done loading
        time.sleep(5)

        # TODO should there have been a driver.close here again?


obs.obs_frontend_add_event_callback(ConnectAndStream)