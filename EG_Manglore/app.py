from datetime import datetime, timedelta
from typing import List
from uuid import UUID
import json
JSON_EXT = ".json"

file_names = ['Danny boy', 'Emma Win', 'Joanna Hef']

# Create an empty dictionary to store data from each file
data_dict = {}
calendarUuidMap = {}

# Iterate through each file
for file_name in file_names:
    with open('./calendr_data/'+file_name+JSON_EXT, 'r') as file:
        # Load JSON data from the file
        data = json.load(file)
        # Add the loaded data to the dictionary with the file name as the key
        data_dict[file_name] = data

# Print the resulting dictionary
for patient in data_dict:
    for appointments in data_dict[patient]['appointments']:
        calendarUuidMap[patient] = appointments['id']
        start_datetime_object = datetime.fromisoformat(appointments['start'])
        end_datetime_object = datetime.fromisoformat(appointments['end'])
        duration = end_datetime_object - start_datetime_object
        #print the data which got for the json.
        print(patient+' - '+' on - '+str(start_datetime_object.day)+'/'+str(start_datetime_object.month)+'/'+str(start_datetime_object.year)+' at '+str(start_datetime_object.hour)+' - '+str(duration))
# print(calendarUuidMap)