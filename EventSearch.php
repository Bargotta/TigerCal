<!-- ****************************************************************************************************

  Left Search Bar on calendar page

**************************************************************************************************** -->
<?php

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

  $output = '';
  // collect keys types
  if (isset($_POST['searchVal'])) {
    $searchq = htmlentities(addslashes($_POST['searchVal']));
    if (empty(trim($searchq))) {
      $query = "SELECT `club`, `eventName` FROM `events` WHERE `date`>NOW() ORDER BY `date`, `time` LIMIT 5";
      $output = printEvents($query, true);
    }
    else {
      $query = "SELECT `club`, `eventName` FROM `events` WHERE (`club` LIKE '%$searchq%' OR `eventName` LIKE '%$searchq%') AND `date`>NOW() LIMIT 7";
      $output = printEvents($query, false);
    }
  }
  echo $output;

  function printEvents($query, $isEmpty) {
      $result = mysqli_query($conn, $query) or die("Could not search! eventsearch".mysqli_error($conn));
      $count = mysqli_num_rows($result);
      $output = '';
        if ($isEmpty) {
            $output = '<b style="font-size:110%;">Upcoming Events:</b>';
            $output .= fetchArray($result);
        }
        else {
          $output .= '<b style="font-size:110%;">'.$count.' Search Results</b><br>'.fetchArray($result);
        }
    return $output;
  }

  function fetchArray($result) {
    $output = '<dl>';
    while($row = mysqli_fetch_array($result)) {
       $club = $row['club'];
       $eventName = $row['eventName'];
       $output .= '<dt><b>'.$club.'</b></dt><dd id="leftBar">'.$eventName.'</dd>';
    }
    $output .= '</dl>';
    return $output;
  }
?>