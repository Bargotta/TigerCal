<!DOCTYPE html>
<html lang="en">
<head>
    <title>thenewboston</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>

 	<link rel="stylesheet" type="text/css" href="CalendarDesign.css">

 	<style type="text/css">
	.event {
		padding: 30px;
		/*background: rgb(230, 230, 230);*/
		border-top: rgb(220, 220, 220) solid 1px;
		border-bottom: rgb(220, 220, 220) solid 1px;
	}

	.first {
		border-top: rgb(220, 220, 220) solid 2px;
	}

	h3 a, a:hover, a:active {
		color: #f4511e;
	}
</style>

</head>
<body>
	<div id="authorize-div" style="display: none">
		<span>Authorize access to Google Calendar </span>
		<!--Button for the user to click to initiate auth sequence -->
		<button id="authorize-button" onclick="handleAuthClick(event)">
			Authorize
		</button>
	</div>
<div class="container">
	<div class="row">
		<?php
			include 'EventsListCalendar.php';
			$calendar = new EventsListCalendar();
			echo $calendar->create();
		?>
		<div class="col-sm-8">
			<!-- Events List -->
			<?php
			    require_once('mysql_connect.php');

			    $order="`club`, `date`, `time`";

			    if (isset($_POST['submit'])) {
			        $order=$_POST['order'];
			    }
			    $query = "SELECT club, eventName, `date`, `time`, `endDate`, `endTime`, location, cost, `student events eligible`, description 
			    	FROM events WHERE `date`>NOW() ORDER BY ".$order."LIMIT 5";

			    $response = mysql_query($query);
			    // Print out table of events
			    if ($response) {
			        while($row = mysql_fetch_array($response)) {

			        	$club = $row['club'];
			        	$clubPrint = addslashes($club);

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

			        	$location = $row['location'];
			        	$locationPrint = addslashes($location);

			        	$description = $row['description'];
			        	$descriptionPrint = addslashes($description);

			        	$cost = $row['cost'];
			        	$studentEventsEligible = $row['student events eligible'];

			        	$insertEvent = "'".$clubPrint."', '".$eventNamePrint.
			            	"', '".$datePrint."', '".$timePrint."', '".$endDatePrint."', '".
			            	$endTimePrint."', '".$locationPrint."', '".$descriptionPrint."'";

			            $eventInfo = '<div class="col-sm-12 event">'.
			            				'<h3 class="EventName-List"><a href="#">'.$club.' / '.$eventName.'</a></h3>'.
										'<div><b><span>'.$longDate.' </span><span>'.$time.'</span><span> - ';
						if ($longDate == $longEndDate) {
							$eventInfo.=$endTime.'</span></b></div><div>'.$description.'</div></div>';
						}
						echo $eventInfo;
			        }
			    }
			    else {
			        echo "Couldn't issue database query ";
			        // echo mysql_error();
			    }
			?>
		</div>
	</div>
</div>


</body>
</html>