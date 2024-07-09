import gspread
from oauth2client.service_account import ServiceAccountCredentials
from pynput import keyboard

# Set up the credentials and the Sheets API client
SCOPES = ['https://www.googleapis.com/auth/spreadsheets.readonly']
SERVICE_ACCOUNT_FILE = 'path/to/your/credentials.json'

credentials = ServiceAccountCredentials.from_json_keyfile_name(SERVICE_ACCOUNT_FILE, SCOPES)
client = gspread.authorize(credentials)

# The ID of the spreadsheet to read
SPREADSHEET_ID = 'your_spreadsheet_id'
RANGE_NAME = 'Sheet1!A1:B2'  # Example range to read

def fetch_google_sheet_data():
    try:
        sheet = client.open_by_key(SPREADSHEET_ID).worksheet('Sheet1')
        values = sheet.get(RANGE_NAME)
        if not values:
            print('No data found.')
        else:
            for row in values:
                print(row)
    except Exception as e:
        print(f'An error occurred: {e}')

# Define the function to be called when F1 is pressed
def on_press(key):
    if key == keyboard.Key.f1:
        print("F1 key pressed. Fetching data from Google Sheets...")
        fetch_google_sheet_data()

# Set up the listener for keyboard events
listener = keyboard.Listener(on_press=on_press)
listener.start()
listener.join()
