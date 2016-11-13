<?php

/****************************************************************************************************

Used to scrape events from princeton event's page

343 events as of 05 Sep 2016
355 events as of 10 Sep 2016 (Database no updated)

****************************************************************************************************/

// get connection
include_once('mysql_connect.php');
// $data = file_get_contents("http://www.princeton.edu/events/?view=weekly&category=all&from=2016-08-10&to=2017-08-09");
$data = file_get_contents("http://www.princeton.edu/events/?from=2016-08-10&to=2017-08-09&organization=&category=all&view=upcoming");

// Primary scrape to get div containing events
$getEventsRegex = "/<div id=\"calendar-results\">(.*)<\\/div>\\s<\\/div><div id=\"subcontent\">/s";
preg_match($getEventsRegex, $data, $divMatch);
$getEvents = $divMatch[1];

// secondary scrape to extract event info
$eventInfoRegex = "/(?:<h3>(.*?)<\\/h3>)*?\\s*?<div class=\"calendar-event vevent\"><span class=\"event-time\"><abbr class=\"dtstart\" title=\".*?\">".
	"(.*?)<\\/abbr>.*?>(.*?)<\\/abbr>.*?\"><a href=\".*?\">(.*?)<\\/a><.*?\">(.*?)<\\/span>.*?\">(.*?)(?:<br>\\s)*?(.*?)(?:<br>\\s)*?(.*?)(?:<br>\\s)*?".
	"(.*?)(?:<br>\\s)*?(.*?)(?:<br>\\s)*?(.*?)(?:<br>\\s)*?<\\/span><\\/div>\\s*?/";

preg_match_all($eventInfoRegex, $getEvents, $eventInfo, PREG_SET_ORDER);

// Non-PHP formatted regex
/*
/(?:<h3>(.*?)<\/h3>)*?\s*?<div class="calendar-event vevent"><span class="event-time"><abbr class="dtstart" title=".*?">(.*?)<\/abbr>.*?>(.*?)
<\/abbr>.*?"><a href=".*?">(.*?)<\/a><.*?">(.*?)<\/span>.*?">(.*?)(?:<br>\s)*?(.*?)(?:<br>\s)*?(.*?)(?:<br>\s)*?(.*?)(?:<br>\s)*?(.*?)(?:<br>\s)*?
(.*?)(?:<br>\s)*?<\/span><\/div>\s*?/
*/

// Print out all event names
// $i=1;
// foreach ($eventInfo as $info) {
// 	echo $i.". ".$info[4]."<br>";
// 	$i++;
// }

// variable to store date of event
$previousDate = "";
$numOFEvents = 0;
foreach ($eventInfo as $info) {
	$eventName = $info[4];
	echo "<b>" . $eventName . "</b>";

	$date = "";
	// if date is not set, set it to previous event's date
	if (empty($info[1])) {
		$date = $previousDate;
		echo "<br>Date: " . $date;
	}
	else {
		// formate into short date Y-m-d
		$fullDate = explode("/", $info[1]);
		$formattedDate = date('Y-m-d', strtotime($fullDate[0]));
		$date = $formattedDate;
		$previousDate = $date;
		echo "<br>Date: " . $date;
	}

	$time = toMilitary($info[2]);
	echo "<br>Start Time: " . $time;

	$endTime = toMilitary($info[3]);
	echo "<br>End Time: " . $endTime;

	$description = $info[5];
	echo "<br>Description: " . $description;

	// all locations for event
	$location1 = $info[6];
	$location2 = $info[7];
	$location3 = $info[8];
	$location4 = $info[9];
	$location5 = $info[10];
	$location6 = $info[11];

	// find out how many locations there are
	$depth = 0;
	$index = 12;
	if (!empty($location6)) {
		$depth++; // 1
		$index--; // 6
		if (!empty($location5)) {
			$depth++; // 2
			$index--; // 5
			if (!empty($location4)) {
				$depth++; // 3
				$index--; // 4
				if (!empty($location3)) {
					$depth++; // 4
					$index--; // 3
					if (!empty($location2)) {
						$depth++; // 5
						$index--; // 2
						if (!empty($location1)) {
							$depth++; // 6
							$index--; // 1
						}
					}
				}
			}
		}
	}

	// string to insert locations into database
	$locInsert = "";
	// print out locations
	for ($i=0; $i < $depth; $index++) {
		$locInsert .= "'".addslashes($info[$index])."', ";
		echo "<br>Location ".++$i.": ".$info[$index];
	}
	// fill in remaining blank locations
	$remaining = 6 - $depth;
	for ($i=0; $i < $remaining; $i++) { 
		$locInsert .= "'', ";
	}
	echo "<br><br>";

	// $insert = "INSERT INTO `events` (`eventName`, `date`, `time`, `endDate`, `endTime`, `location1`, `location2`, `location3`, `location4`, `location5`, ".
	// 	"`location6`, `description`) VALUES ('".addslashes($eventName)."', '".addslashes($date)."', '".addslashes($time)."', '".
 //    	addslashes($date)."', '".addslashes($endTime)."', ".$locInsert."'".addslashes($description)."')";

	// mysql_query($insert, $dbc);
	$numOFEvents++;
}

echo "Total number of events: ".$numOFEvents;

function toMilitary($time) {
    // separate time into hours and minutes
    $separate = explode(":", $time);
    $hour = intval($separate[0]);

    // Determine if time is am or pm
    $identifier = explode(" ", $separate[1]);
    $minutes = $identifier[0];

    $pm = strpos($separate[1], 'p.m');

    if ($pm == 3 && $hour != 12) {
        $hour += 12;
    }
    elseif ($pm != 3 && $hour == 12) {
        $hour -= 12;
    }
    return $hour.':'.$minutes;
}
?>