<?php

// create a function which will process the data and get back the available time 
function findAvailableTime($calendarIds, $duration, $periodToSearch, $timeSlotType = null) {
    // assign the all the required data by fetching the form json file using the function loadCalendarData()
    $calendarData = loadCalendarData();
    // Parse period to search
    list($startTime, $endTime) = explode('/', $periodToSearch);
    $startTime = new DateTime($startTime);
    $endTime = new DateTime($endTime);

    // Initialize empty array to store available time slots
    $availableTimeSlots = [];
    // print_r($calendarData);exit;
    // Iterate through calendar data
    foreach ($calendarData as $person => $data) {
        // Retrieve appointments and timeslots for the current person
        $appointments = $data['appointments'];
        $timeslots = $data['timeslottypes'];
        
        // Check if the person's calendar ID is in the list of calendars to search
        if (in_array($person, $calendarIds)) {
            // Iterate through appointments
            foreach ($appointments as $appointment) {
                $start = new DateTime($appointment['start']);
                $end = new DateTime($appointment['end']);
    
                // Check if appointment overlaps with the period to search
                if ($start < $endTime && $end > $startTime) {
                    // Appointment overlaps, so this time slot is not available
                    continue 2; // Continue to the next appointment
                }
            }
    
            // No overlapping appointments found within the period, check timeslots if any
            foreach ($timeslots as $timeslot) {
                $start = new DateTime($timeslot['start']);
                $end = new DateTime($timeslot['end']);
    
                // Check if timeslot is within the period to search
                if ($start >= $startTime && $end <= $endTime) {
                    // Check if the timeslot type matches if specified
                    if ($timeSlotType !== null && $timeslot['id'] != $timeSlotType) {
                        continue; // Timeslot type doesn't match, skip
                    }
    
                    // Check if duration of timeslot is sufficient
                    $slotDuration = $start->diff($end)->i; // Duration in minutes
                    if ($slotDuration >= $duration) {
                        // Add available time slot
                        $availableTimeSlots[] = [
                            'start' => $start->format('Y-m-d H:i:s'),
                            'end' => $end->format('Y-m-d H:i:s')
                        ];
                    }
                }
            }
        }
    }
    // Return available time slots
    return $availableTimeSlots;
}

// function to get the clender data form the json files which is locateed in the directory "calendr_data" form ther we are getting the all the json files and fetching the details
function loadCalendarData() {
    $data = [];
    $calendarFiles = glob('./calendr_data/*.json');
    foreach ($calendarFiles as $file) {
        $content = file_get_contents($file);
        $calendarId = basename($file, '.json');
        $data[$calendarId] = json_decode($content, true);
    }
    return $data;
}

// declareing the neccessarry variables required for the further program execution.
$data_dict = [];
$calendarUuidMap = [];
$calendarIds = [];
$duration = null;
$periodToSearch = null;
$timeSlotType = null;

// creating the data directory by fetching the details form the json files
$data_dict = loadCalendarData();
// print_r($data_dict);exit;
foreach ($data_dict as $patient => $patient_data) {
    foreach ($patient_data['appointments'] as $appointments) {
        $calendarIds[$patient] = $appointments['id'];
        $start_datetime_object = new DateTime($appointments['start']);
        $end_datetime_object = new DateTime($appointments['end']);
        $periodToSearch = $start_datetime_object->format('Y-m-d H:i:s').'/'. $end_datetime_object->format('Y-m-d H:i:s');
        $duration = $end_datetime_object->diff($start_datetime_object)->i;
        $timeSlotType = null;

        // echo $patient.' - on - '.$start_datetime_object->format('d/m/Y').' at '.$start_datetime_object->format('H').':'.$start_datetime_object->format('i').' - '.$duration->format('%h:%I')."\n";
    }
}

// function call to check availability for the time
$availableTimeSlots = findAvailableTime($calendarIds, $duration, $periodToSearch, $timeSlotType);
print_r($availableTimeSlots);

?>
