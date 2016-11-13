<?php

session_start();

/******************************************************************************************

Shows events on the Events List Page

******************************************************************************************/

include_once('mysql_connect.php');

// $servername = "198.57.247.248";
// $username = "tigercal";
// $password = "Iydsafttata96";
// $database = "website";

// // Create connection
// $conn = mysqli_connect($servername, $username, $password, $database);

// // Check connection
// if (!$conn) {
//     die("Connection failed: " . mysqli_connect_error());
// }

// gets order from select input
$order = $_SESSION["order"];
if (!trim($order)) {
    $order="`date`, `time`";
}

if (isset($_POST['dayIDCalendar2'])) {
    $dayIDCalendar2 = htmlentities(addslashes($_POST['dayIDCalendar2']));

    $query = "SELECT * FROM events WHERE `date`='".$dayIDCalendar2."' ORDER BY ".$order;
    $response = mysqli_query($conn, $query);

    printEvents($response, false);
}

// When user types in event
if (isset($_POST['searchVal'])) {
    $searchq = htmlentities(addslashes($_POST['searchVal']));

    if (empty(trim($searchq))) {
        $query = "SELECT * FROM `events` WHERE `date`>NOW() ORDER BY ".$order;
        $response = mysqli_query($conn, $query);
        printEvents($response, true);
    }
    else {
        $query = "SELECT * FROM `events` WHERE (`club` LIKE '%$searchq%' OR `eventName` LIKE '%$searchq%') AND `date`>NOW() ORDER BY ".$order;
        $response = mysqli_query($conn, $query);
        printEvents($response, false);
    }
}

function printEvents($response, $isEmpty) {
    
    $count = mysqli_num_rows($response);
    // $output = '<table class="table table-hover">'.
    //             '<thead>'.
    //                 '<tr>'.
    //                     '<td><b>count</b></td>'.
    //                 '</tr>'.
    //             '</thead>'.
    //             '<tbody>'.
    //                 '<tr>'.
    //                     '<td>'.$count.'</td>'.
    //                 '</tr>'.
    //             '</tbody>'.
    //         '</table>';
    if (!$isEmpty) {
        if ($count == 1)
            echo '<b id="count">'.$count.' Search Result</b></div><div class="col-sm-12 EventsListDiv" id="EventsListDiv">';
        else
            echo '<b id="count">'.$count.' Search Results</b></div><div class="col-sm-12 EventsListDiv" id="EventsListDiv">';
    }
    else
        echo '</div><div class="col-sm-12 EventsListDiv" id="EventsListDiv">';

    // Print out table of events
    if ($response) {

        while($row = mysqli_fetch_array($response)) {

            $club = $row['club'];
            $clubPrint = addslashes($club);
            if (!empty($club)) {
                $club.=" / ";
            }

            $eventName = $row['eventName'];
            $eventNamePrint = addslashes($eventName);

            $date = $row['date'];
            $datePrint = addslashes($date);
            $longDate = date('d M Y', strtotime($date));

            $time = $row['time'];
            $timePrint = addslashes($time);

            $endDate = $row['endDate'];
            $endDatePrint = addslashes($endDate);
            $longEndDate = date('d M Y', strtotime($endDate));

            $endTime = $row['endTime'];
            $endTimePrint = addslashes($endTime);

            $location1 = $row['location1'];
            $location1Print = addslashes($location1);
            if (!empty($location1)) {
                $location1 = '<b class="middot"> &middot; </b><span class="loc">'.$location1.'</span>';
            }

            $location2 = $row['location2'];
            $location2Print = addslashes($location2);
            if (!empty($location2)) {
                $location2 = '<b class="middot"> &middot; </b><span class="loc">'.$location2.'</span>';
            }

            $location3 = $row['location3'];
            $location3Print = addslashes($location3);
            if (!empty($location3)) {
                $location3 = '<b class="middot"> &middot; </b><span class="loc">'.$location3.'</span>';
            }

            $location4 = $row['location4'];
            $location4Print = addslashes($location4);
            if (!empty($location4)) {
                $location4 = '<b class="middot"> &middot; </b><span class="loc">'.$location4.'</span>';
            }

            $location5 = $row['location5'];
            $location5Print = addslashes($location5);
            if (!empty($location5)) {
                $location5 = '<b class="middot"> &middot; </b><span class="loc">'.$location5.'</span>';
            }

            $location6 = $row['location6'];
            $location6Print = addslashes($location6);
            if (!empty($location6)) {
                $location6 = '<b class="middot"> &middot; </b><span class="loc">'.$location6.'</span>';
            }

            $description = $row['description'];
            $descriptionPrint = addslashes($description);
            $descriptionPrint = htmlspecialchars($descriptionPrint);

            $cost = $row['cost'];
            $studentEventsEligible = $row['student events eligible'];

            $insertEvent = "'".$eventNamePrint.
                "', '".$datePrint."', '".$timePrint."', '".$endDatePrint."', '".
                $endTimePrint."', '".$location1Print."', '".$descriptionPrint."'";


            $eventInfo ='<div class="col-sm-12 event">'.
                            '<h3 class="EventName-List">'.
                                '<a href="#" type="link" onclick="showDetails(this)" data-toggle="modal" data-eventclub="'.$clubPrint.
                                '" data-eventname="'.$eventNamePrint.'" data-date="'.$longDate.'" data-time="'.$timePrint.
                                '" data-enddate="'.$longEndDate.'" data-endtime="'.$endTimePrint.'" data-location1="'.$location1Print.
                                '" data-location2="'.$location2Print.'" data-location3="'.$location3Print.'" data-location4="'.$location4Print.
                                '" data-location5="'.$location5Print.'" data-location6="'.$location6Print.'" data-description="'.$descriptionPrint.
                                '" data-target="#EventInfoModal" class="open-EventInfoModal">'.$club.$eventName.'</a>'.
                            '</h3>'.
                            '<div><b><span>'.$longDate.' </span><span>'.$time.'</span> - ';

            if ($longDate == $longEndDate) {
                $eventInfo.=$endTime.'</b>'.$location1.'</div><div>'.$description.'</div></div>';
            }
            else
                $eventInfo.=$longEndDate.' '.$endTime.'</b>'.$location1.'</div><div>'.$description.'</div></div>';

            echo $eventInfo;

        }
    }
    else {
        echo "Couldn't issue database query ".mysql_error($conn);
    }
}

?>