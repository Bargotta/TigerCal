<?php

session_start();

/*******************************************************************************************

Relies on:

CalendarScript.php -> Creates the main interactive calendar for calendar's page
EventSearch.php -> Left Search Bar on calendar page
TodaysEvents.php -> Right side bar on Calendar Page

EventsListcalendar.php -> Calendar for the Events List Page
club_regex.php -> Used to scrape events from princeton event's page

AddToWebsiteDB.php -> allows clubs to add events on the Clubs and Organisations page
EventEdit.php -> Edits Events on the Clubs and Organisations page

*******************************************************************************************/
ob_start();

// import phpCAS lib
include_once('phpCAS-master/CAS.php');

// create connection
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


phpCAS::setDebug();

// initialize phpCAS
phpCAS::client(CAS_VERSION_2_0,'cast.cs.princeton.edu',443,'cas');

// no SSL validation for the CAS server
phpCAS::setNoCasServerValidation();

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>TigerCal</title>
	<meta charset="utf-8">
	<meta name="author" content="Aaron Bargotta">	
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- Links to Montserrat and Lato fonts -->
	<link href="http://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet" type="text/css">
	<link href="http://fonts.googleapis.com/css?family=Lato" rel="stylesheet" type="text/css">
	<!-- Links to bootstrap and JQuery -->
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>

	<!-- Links to an external Style Sheet -->
 	<link rel="stylesheet" type="text/css" href="CalendarDesign.css">
</head>
<body id="myPage" data-spy="scroll" data-target=".navbar" data-offset="60">
	<!--================================================ Welcome Header =================================================-->
	<div class="jumbotron text-center">
	  	<h1>TigerCal</h1> 
	  	<p>Never miss an event again</p> 
	  	<!-- <p>Stay up-to-date with on campus events</p> -->
	</div>

	<!--================================================ Navbar ======================================================-->
	<nav class="navbar navbar-default navbar-fixed-top">
	  	<div class="container">
	    	<div class="navbar-header">
		      	<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
		        	<span class="icon-bar"></span>
		        	<span class="icon-bar"></span>
		        	<span class="icon-bar"></span> 
		      	</button>
		      	<a class="navbar-brand" href="#">tigerCal</a>
	    	</div>
	    	<div class="collapse navbar-collapse" id="myNavbar">
		      	<ul class="nav navbar-nav navbar-left">
			        <!-- <li><a href="#calendar">CALENDAR</a></li> -->
			        <li><a href="#events">EVENT LIST</a></li>
			        <li><a href="#previous_events">PREVIOUS EVENTS</a></li>
			        <li><a href="#about">ABOUT</a></li>
			        <li><a href="phpCAS-master/CASCalendar.php/">CLUBS</a>
			        <!-- <li><a href="#contact">CONTACT</a></li> -->
					<?php
					if (phpCAS::isAuthenticated()) {
						// logout if desired
						if (isset($_REQUEST['logout'])) {
						 	phpCAS::logout();
						}
						echo '<li><a href="#clubs">CLUBS/ORGANIZATIONS</a></li>'.
							'<li><a href="?logout="><b>'.phpCAS::getUser().'</b></a></li>';
					}
					else {
						echo '</ul>
							<ul class="nav navbar-nav navbar-right">
								<li><a href="phpCAS-master/CASCalendar.php/">'.
									'<span class="glyphicon glyphicon-log-in"></span> Login</a></li>';
					}					
					?>
				</ul>
	    	</div>
	  	</div>
	</nav>
	<!-- ================================================ Calendar Page (1) ================================================ -->
	<?php 
	// ***************************** DISABLED CALENDAR PAGE *****************************
	if (false) { ?> 
	<div id="calendar" class="container-fluid">
		<div class="row">
			<!-- ================================================ Left Sidebar ================================================ -->
		    <div class="col-sm-3 sidenav leftSidenav">
				<form class="form" role="form">
				    <label class="col-sm-12 control-label">Search for event:</label>
				    <div class="col-sm-12">
				    	<!-- Event Searching -->
				        <!-- <input class="form-control" name="search" autocomplete="off" onkeyup="searchq();" type="text" placeholder="Event search..."> -->
				    </div>
				</form>
    			<div class="col-sm-12" id="output">
			      	<?php
				        include "EventSearch.php";
				        $query = "SELECT `club`, `eventName` FROM `events` WHERE `date`>NOW() ORDER BY `date`, `time` LIMIT 5";
				        $output = printEvents($query, true);
				        echo $output;
			      	?>
    			</div>
		    </div>
		    <div id="infoModal" class="modal fade" role="dialog">
			  	<div class="modal-dialog">

				    <!-- Modal content-->
				    <div class="modal-content">
				      	<div class="modal-header">
				        	<button type="button" class="close" data-dismiss="modal">&times;</button>
				        	<h4 class="modal-title">Event Info:</h4>
				      	</div>
				      	<div class="modal-body">
				      		<p>Event: <span id="infoEventName">...</span></p>
					        <p>Club: <span id="infoClubName">...</span></p>
					        <p>Location: <span id="infoLocation">...</span></p>
					        <p>Start Date: <span id="infoStartDate">...</span> Start Time: <span id="infoStartTime">...</span></p>
					        <p>End Date: <span id="infoEndDate">...</span> End Time: <span id="infoEndTime">...</span></p>
			      		</div>
			      		<div class="modal-footer">
			        		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			      		</div>
			    	</div>
			  	</div>
			</div>

		    <!-- ================================================ Include Calendar ================================================ -->
			<?php
				include 'CalendarScript.php';
				$calendar = new CalendarScript();
				echo $calendar->create();
			?>

			<!-- ================================================ Right Sidebar ================================================ -->
			<div class="col-sm-3 sidenav rightSidenav">
			    <h2>Events Available</h2>    
			    <div>
			    	Hover over each day to see what events are going on!
			    </div>
			    <div id="eventlist"><!-- Events listed here --></div>
			</div>
		</div>
	</div>
	<?php } ?> 

	<!-- ================================================ Values Section ================================================ -->
<!-- 	<div class="container-fluid bg-grey">
		<div class="row">
			<div class="col-sm-4">
				<span class="glyphicon glyphicon-globe logo"></span>
			</div>
			<div class="col-sm-8">
				<h2>Nice place to add something</h2>
				<h4><strong>Princeton:</strong> Quote?</h4> 
				<p><strong>Events:</strong> Add a nice quote about opportunities</p>
			</div>
		</div>
	</div> -->

	<!-- ================================================ Events List Page (2) ================================================ -->
	<div id="events" class="container-fluid">
		<!-- <h2 class="text-center">COMPLETE EVENTS LIST</h2> -->
		<!-- <h4 class="text-center">Add events to your Google Calendar</h4> -->
		<br>
		<div class="row">
	 		<?php
	 			include 'EventsListCalendar.php';
				$calendar = new EventsListCalendar();
				echo $calendar->create();
			?>
			<div class="col-sm-8">
				<!-- Events List -->
				<form action="index.php#events" method="post">
					<div class="col-sm-12">
					    <div class="col-sm-6">
					    	<div class="col-sm-4" style="padding:0">
								Quick Search: 
							</div>
							<div class="col-sm-8" style="padding:0">
								<input class="form-control" name="search" autocomplete="off" onkeyup="searchq();" type="text" placeholder="Event search...">
				        	</div>
				        </div>
						<div class="col-sm-6">
				        	Order By:
				            <select name="order">
				                <option value="`club`, `date`, `time`"></option>                
				                <option value="`club`, `date`, `time`">Club</option>
				                <option value="`cost`, `date`, `time`">Cost</option>
				                <option value="`date`, `time`">Time</option>
				                <option value="`eventName`, `date`, `time`">Event</option>
				                <option value="`location1`, `date`, `time`">Location</option>
				                <option value="`student events eligible` DESC, cost, `date`, `time`">Student Events Eligible</option>
				            </select>
					        <input type="submit" name="submitOrder" value="search" />
					    </div>
					</div>
			    </form>
				<div id="authorize-div" style="display: none">
					<span>Authorize access to Google Calendar </span>
					<!--Button for the user to click to initiate auth sequence -->
					<button id="authorize-button" onclick="handleAuthClick(event)">
					Authorize
					</button>
				</div>
				<!-- Actual Calendar: first div to display count/ second div to show events -->
				<div class="col-sm-12" id="EventsListCount">
					<?php
					// get order selected by user and pass it to EventsListPage.php
				    $order="`date`, `time`";
				    if (isset($_POST['submitOrder'])) {
				        $order=$_POST['order'];
				    }
				    // send order to EventsListPage.php
				    $_SESSION["order"] = $order;

				    $query = '';
				    // check if day has been selected on the calendar
			        if (isset($_GET['date'])) {
			            $chosenDate = $_GET['date'];
                        $date = explode('-', $chosenDate);
		            	$currentYear  = $date[0];
		            	$currentMonth = $date[1];
		            	$currentDay   = $date[2];

		            	// show events for the entire month if selected
		            	if ($currentDay == '0') {
		            		$startDate = $currentYear.'-'.$currentMonth.'-01';
		            		$endDate = $currentYear.'-'.$currentMonth.'-31';
						    $query = "SELECT * FROM events WHERE (`date` BETWEEN '".$startDate."' AND '".$endDate."') AND (`date` > NOW()) ORDER BY ".$order;
		            	}
		            	else
							$query = "SELECT * FROM events WHERE `date`='".$chosenDate."' ORDER BY ".$order;
				        $response = mysqli_query($conn, $query);
					    $count = mysqli_num_rows($response);
				        if ($count == 1)
				            echo '<b id="count">'.$count.' Search Result</b>';
				        else
				            echo '<b id="count">'.$count.' Search Results</b>';
			        }
			        // default query if no date is selected
			        else {
			        	// echo '</div>';
					    $query = "SELECT * FROM events WHERE `date`>NOW() ORDER BY ".$order;
			        }
			//  </div> closed with echo
					?>
					<div class="col-sm-12 EventsListDiv" id="EventsListDiv">
						<?php
						if (true) {

						    // $query = '';
					     //    if (isset($_GET['date2'])) {
					     //        $chosenDate = $_GET['date2'];
							   //  $query = "SELECT * FROM events WHERE `date`='".$chosenDate."' ORDER BY ".$order;
					     //    }
					     //    else
							   //  $query = "SELECT * FROM events WHERE `date`>NOW() ORDER BY ".$order;

						 //    // gets id of cell clicked on calendar 2
							// if (isset($_POST['dayIDCalendar2'])) {
							// 	$dayIDCalendar2 = htmlentities(addslashes($_POST['dayIDCalendar2']));
						 //    	$query = "SELECT * FROM events WHERE `date`".$dayIDCalendar2." ORDER BY ".$order;
							// }
						    $response = mysqli_query($conn, $query);
						    // Print out table of events
						    if ($response) {

						        while($row = mysqli_fetch_array($response)) {

						        	$eventId = $row['eventID'];
						        	$eventIdPrint = addslashes($eventId);

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
						        echo "Couldn't issue database query ";
						        echo mysqli_error($conn);
						    }
						}
						?>
					</div>
				</div>
			</div>
		</div>
<!-- 		<br><br>
		<div class="row">
			<div class="col-sm-4">
			  	<span class="glyphicon logo-small glyphicon-leaf"></span>
			  	<h4>GREEN</h4>
			  	<p>Combats mass emails</p>
			</div>
			<div class="col-sm-4">
			  	<span class="glyphicon logo-small glyphicon-certificate"></span>
			  	<h4>CERTIFIED</h4>
			  	<p>We work with clubs and organizations to keep Princeton+ updated</p>
			</div>
			<div class="col-sm-4">
			  	<span class="glyphicon logo-small glyphicon-wrench"></span>
			  	<h4>HARD WORK</h4>
			  	<p>We know you work hard, so take time to enjoy yourself</p>
			</div>
		</div> -->
	</div>

	<!-- Modal for events info (Events List Page) -->
	<div id="EventInfoModal" class="modal fade" role="dialog">
	  	<div class="modal-dialog">

	    	<!-- Modal content-->
	    	<div class="modal-content">
	      		<div class="modal-header">
	        		<button type="button" class="close" data-dismiss="modal">&times;</button>
	        		<h4 class="modal-title"><b><span id="infoEventName" style="color:#f4511e;"></span></b></h4>
	      		</div>
	      		<div class="modal-body">
	      			<div class="col-sm-12 details">
	      				<div class="col-sm-6">
		      				<b style="font-size:120%;">Details</b>
					        <br><b>Date:</b><br><span id="infoStartDate"></span> 
					        <!-- <p>End Date: <span id="infoEndDate"></span></p>  -->
					        <br><b>Time:</b><br><span id="infoStartTime"></span>
					        - <span id="infoEndTime"></span>      
				      		<br><b>Event Category:</b><br><span id="infoCategory">Athletics</span>
					    </div>
					    <div class="col-sm-6">	
					    	<b>Organization:</b><br><span id="infoClub">*Club Name*</span>
					        <br><b>Location:</b><br> 
					        <span id="infoLocation1"></span>
					    </div>
				    </div>
    				<b>Description:</b><br><span id="infoDescription"></span>
    				<br><button id="googleCalButton">+Google Calendar</button>
			    </div>
	      		<div class="modal-footer">
	        		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	      		</div>
	    	</div>

	  	</div>
	</div>

	<!-- ================================================ Previous Events Page (3) ================================================ -->
	<div id="previous_events" class="container-fluid text-center bg-grey">
		<!-- <h2>Previous Events</h2> -->
		<h2>Relive the moment</h2>
		<!-- <h4><b>Relive the moment</b></h4> -->
		<!-- Carousel -->
		<h4><b>Events during the past week</b></h4>
		<div id="myCarousel" class="carousel slide text-center" data-ride="carousel">
		  	<!-- Indicators -->
		  	<ol class="carousel-indicators">
			    <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
			    <li data-target="#myCarousel" data-slide-to="1"></li>
			    <li data-target="#myCarousel" data-slide-to="2"></li>
		  	</ol>

		  	<!-- Wrapper for slides -->
		  	<div class="carousel-inner" role="listbox">
		    	<div class="item active">
		  			<div class="thumbnail">
		    			<img class="img-circle img-responsive movingPic" src="summertheatre.jpg" alt="Theatre Intime">
		    			<p><strong>Theatre Intime</strong></p>
		  			</div>
		    	</div>
		    	<div class="item">
		    		<div class="thumbnail">
		    			<img class="img-circle img-responsive movingPic" src="puo.jpg" alt="Princeton University Orchestra">
		    			<p><strong>Princeton University Orchestra</strong></p>	    	
		    		</div>
		    	</div>
		    	<div class="item">
		    		<div class="thumbnail">
		    			<img class="img-circle img-responsive movingPic" src="masflow.jpg" alt="Mas Flow">
		    			<p><strong>Mas Flow</strong></p>	    	
		    		</div>	    	
		    	</div>
		  	</div>

		  	<!-- Left and right controls -->
		  	<a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
		    	<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
		    	<span class="sr-only">Previous</span>
		  	</a>
		  	<a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
		    	<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
		    	<span class="sr-only">Next</span>
		  	</a>
		</div>
		<div class="row text-center">
			<div class="col-sm-4">
	  			<div class="thumbnail">
	    			<img class="img-square img-responsive" src="disiac.jpg" alt="diSiac" />
	    			<p><strong>diSiac</strong></p>
	    			<p>With a murderer on the loose, detective Johnson goes about collecting evidence with an unusual twist</p>
	  			</div>
			</div>
			<div class="col-sm-4">
	 			<div class="thumbnail">
	    			<img class="img-square img-responsive" src="bodyhype.jpg" alt="BodyHype" />
					<p><strong>BodyHype</strong></p>
					<p>BodyHype performed their amazing "Summer Hype!" about a group of friends who go on an extraordinary adventure</p>
	  			</div>
			</div>
			<div class="col-sm-4">
	  			<div class="thumbnail">
	    			<img class="img-square img-responsive" src="columbia.jpg" alt="Theatre Intime" />
	    			<p><strong>Theatre Intime</strong></p>
	    			<p>Theatre Intime (successfully) tackled the whole of human existence in its production of Thornton Wilder's classic play "The Skin of Our Teeth"</p>
	  			</div>
			</div>
		</div>

	</div>
	<?php if (false) { ?>
	<!-- ================================================ About Page (4) ================================================ -->
	<div id="about" class="container-fluid">
	  	<div class="row">
	    	<div class="col-sm-8">
	      		<h2>About Tiger Events</h2><br>
	      		<h4>Tiger Events is a student made web application with the intent to be a source of all student ran events on campus.</h4><br>
	      		<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
	    	</div>
	    	<div class="col-sm-4">
	      		<span class="glyphicon glyphicon-signal logo"></span>
	    	</div>
	  	</div>
	</div>
		<!-- Set height and width with CSS for Google Maps-->
	<div id="googleMap" style="height:400px;width:100%;"></div>

	<?php } ?>

	<?php

	if (phpCAS::isAuthenticated()) {?>
	<!-- ================================================ Contact Page (5) ================================================ -->
	<!-- ************************************ CONTACT PAGE IS DISABLED ************************************ -->
	<div id="contact" class="container-fluid bg-grey">
	  	<h2 class="text-center">CONTACT</h2>
	  	<div class="row">
	    	<div class="col-sm-5">
	      		<p>Contact us and we'll get back to you within 24 hours.</p>
				<p><span class="glyphicon glyphicon-map-marker"></span> New Jersey, US</p>
				<p><span class="glyphicon glyphicon-phone"></span> +01 609 933 2287</p>
				<p><span class="glyphicon glyphicon-envelope"></span> bargotta@princeton.edu</p> 
	    	</div>
	    	<div class="col-sm-7">
	      		<div class="row">
	        		<div class="col-sm-6 form-group">
	          			<input class="form-control" id="name" name="name" placeholder="Name" type="text" required>
	        		</div>
	        		<div class="col-sm-6 form-group">
	          			<input class="form-control" id="email" name="email" placeholder="Email" type="email" required>
	        		</div>
	      		</div>
		      	<textarea class="form-control" id="comments" name="comments" placeholder="Comment" rows="5"></textarea><br>
		      	<div class="row">
		        	<div class="col-sm-12 form-group">
		          		<button class="btn btn-default pull-right" type="submit">Send</button>
		        	</div>
		      	</div> 
	    	</div>
	  	</div>
	</div>

	<!-- Set height and width with CSS for Google Maps-->
	<div id="googleMap" style="height:400px;width:100%;"></div>

	<!-- ================================================ Club and Organisations page (6) ================================================ -->
	<div id="clubs" class="container-fluid">
		<div class="row">
			<h2 class="text-center">CLUBS AND ORGANIZATIONS</h2>
			<!-- ================================================ Add Event ================================================ -->
			<div class="col-sm-4 bg-grey">
			    <h2>Add Your Events</h2>

			    <form action="AddToWebsiteDB.php" method="post" class="text-left">
			    <p>Club Name:
			        <input type="text" name="club" size="30" value="" required/>
			    </p>
			    <p>Email:
			        <input type="email" name="email" size="30" value="" required/>
			    </p>
			    <p>Event Name:
			        <input type="text" name="eventName" size="30" value="" required/>
			    </p>
			    <p>Event Start Date:
			        <input type="date" name="date" size="30" value="" maxlength="10" minlength="10" required/>
			    </p>
			    <p>Event Start Time:
			        <input type="time" name="time" size="30" value="" maxlength="8" minlength="8" required/>
			    </p>
			    <p>Event End Date:
			        <input type="date" name="endDate" size="30" value="" maxlength="10" minlength="10" required/>
			    </p>
			    <p>Event End Time:
			        <input type="time" name="endTime" size="30" value="" maxlength="8" minlength="8" required/>
			    </p>
			    <p>Location 1:
			        <input type="text" name="location1" size="30" value="" required/>
			    </p>
			    <p>Cost: $
			        <input type="number" min="0.00" step="0.01" name="cost" size="5" value="0.00" required/>
			    </p>
			    <p>Student Events Eligible:
			        <br><input type="radio" name="eligible" size="3" value="YES" required>YES<br>
			        <input type="radio" name="eligible" size="3" value="NO" required>NO
			    </p>
			    <p>Brief Description:
  					<textarea class="form-control" rows="3" id="description" name="description" size="50" placeholder="No more than 50 characters..." required></textarea>
			    </p>
				<p>
				    <input type="submit" name="AddEvent" value="Add Event" />
				</p>
				</form>
			</div>
			<!-- ================================================ Edit Event Table ================================================ -->
			<div class="col-sm-8">
			    <h2>Edit Your events</h2>
			    <form action="#clubs" method="post">
			    	<h4 class="text-left"><b>Current Events: </b>
			    	<input type="text" name="clubName"></input><input type="submit" name="submitClubName" value="search"></input></h4>
				</form>
			    <p class="text-left">
			    	<!-- Search for events posted form a club -->
				    <?php
				    	$clubName = '';
						if (isset($_POST['clubName'])) {
							$clubName = addslashes(htmlentities($_POST['clubName']));
						}

						require_once('../mysql_connect.php');
						// Change back once incorporated clubs
					    $order="`date`, `time`";
					    // $order="`eventID`";

					    $query = "SELECT * FROM events WHERE (`club` LIKE '%".$clubName."%') OR (`eventName` LIKE '%".$clubName."%') ".
					    	"ORDER BY ".$order;

					    $response = mysql_query($query);
					    // Print out table of events
					    if ($response) {

					        echo '
	    						<table class="table table-hover">
	    						<thead>
						        	<tr>
							        	<td style="display:none;"><b>eventID</b></td>
							        	<td><b>Club</b></td>
							        	<td style="display:none;"><b>email</b></td>
							            <td><b>Event</b></td>
							            <td><b>Start Date</b></td>
							            <td><b>Start Time</b></td>
							            <td><b>End Date</b></td>
							            <td><b>End Time</b></td>
							            <td><b>Location1</b></td>
							            <td><b>Location2</b></td>
							            <td><b>Location3</b></td>
							            <td><b>Location4</b></td>
							            <td><b>Cost</b></td>
							            <td><b>Student Events Eligible</b></td>
							            <td><b>Brief Description</b></td>
							        </tr>
							    </thead>
							    <tbody>';

					        while($row = mysql_fetch_array($response)) {

					        	$eventID = isset($row['eventID'])?$row['eventID']:"";
					        	$club = isset($row['club'])?$row['club']:"";
					        	$email = isset($row['email'])?$row['email']:"";
					        	$eventName = isset($row['eventName'])?$row['eventName']:"";
					        	$date = isset($row['date'])?$row['date']:"";
			        			$longDate = date('d M Y', strtotime($date));
					        	$time = isset($row['time'])?$row['time']:"";
					        	$endDate = isset($row['endDate'])?$row['endDate']:"";
			        			$longEndDate = date('d M Y', strtotime($endDate));
					        	$endTime = isset($row['endTime'])?$row['endTime']:"";
					        	$location1 = isset($row['location1'])?$row['location1']:"";
					        	$location2 = isset($row['location2'])?$row['location2']:"";
					        	$location3 = isset($row['location3'])?$row['location3']:"";
					        	$location4 = isset($row['location4'])?$row['location4']:"";
					        	$description = isset($row['description'])?$row['description']:"";
					        	$cost = isset($row['cost'])?$row['cost']:"";
					        	$studentEventsEligible = isset($row['student events eligible'])?$row['student events eligible']:"";

					        	// dont need class "email"
					            echo '<tr id="editEventTable" class="'.$email.'"><td style="display:none;">' . 
					            $eventID . '</td><td>' .
					            $club . '</td><td style="display:none;">' .
					            $email . '</td><td>' .
					            $eventName . '</td><td>' .
					            $longDate . '</td><td>' .
					            $time . '</td><td>' .
					            $longEndDate . '</td><td>' .
					            $endTime . '</td><td>' .
					            $location1 . '</td><td>' .
					            $location2 . '</td><td>' .
					            $location3 . '</td><td>' .
					            $location4 . '</td><td>$' .
					            $cost . '</td><td>' .
					            $studentEventsEligible . '</td><td>' .
					            $description . '</td>';
					            echo '</tr>';
					        }
					        echo '<tbody></table>';
					    }
					    else {
					        echo "Couldn't issue database query ";
					        // echo mysql_error($dbc);
					    }
				    ?>
					<!-- ================================================ Modal ================================================ -->
					<div id="myModal" class="modal fade" role="dialog">
					  	<div class="modal-dialog">

						    <!-- Modal content-->
						    <div class="modal-content">
						      	<div class="modal-header">
						        	<button type="button" class="close" data-dismiss="modal">&times;</button>
						        	<h4 class="modal-title">Edit Event</h4>
						      	</div>
								<!-- ================================================ Update Events ================================================ -->
						      	<div class="modal-body">
							        <p>
										<form action="EventEdit.php" method="post" class="text-left">
											<p>
												<input type="hidden" name="eventIDEdit" id="eventIDEdit" />
											</p>
										    <p>Club Name:
										        <input type="text" name="clubEdit" id="club" size="30" required/>
										    </p>
										    <p>Email:
										        <input type="email" name="emailEdit" id="editEmail" size="50" />
										    </p>
										    <p>Event Name:
										        <input type="text" name="eventNameEdit" id="eventName" size="30" value="" required/>
										    </p>
										    <p>Event Start Date:
										        <input type="date"  name="dateEdit" id="date" size="30" required/>
										    </p>
										    <p>Event Start Time:
										        <input type="time" name="timeEdit" id="time" size="30" value="" maxlength="8" minlength="8" required/>
										    </p>
										    <p>Event End Date:
										        <input type="date" name="endDateEdit" id="endDate" size="30" value="" maxlength="10" minlength="10" required/>
										    </p>
										    <p>Event End Time:
										        <input type="time" name="endTimeEdit" id="endTime" size="30" value="" maxlength="8" minlength="8" required/>
										    </p>
										    <p>Location 1:
										        <input type="text" name="location1Edit" id="location1" size="30" value="" />
										    </p>
										    <p>Location 2:
										        <input type="text" name="location2Edit" id="location2" size="30" value="" />
										    </p>
										    <p>Location 3:
										        <input type="text" name="location3Edit" id="location3" size="30" value="" />
										    </p>
										    <p>Location 4:
										        <input type="text" name="location4Edit" id="location4" size="30" value="" />
										    </p>
										    <p>Cost: $
										        <input type="number" min="0.00" step="0.01" name="costEdit" id="cost" size="5" value="0.00" />
										    </p>
										    <p>Student Events Eligible:
										        <br>
										        <input type="radio" name="eligibleEdit" id="yes" size="3" value="YES" >YES<br>
										        <input type="radio" name="eligibleEdit" id="no" size="3" value="NO" >NO
										    </p>
										    <p>Brief Description:
							  					<textarea class="form-control" rows="3" id="briefDesc" name="descriptionEdit" size="50" placeholder="No more than 50 characters..." required></textarea>
										    </p>
											<p>
											    <input type="submit" name="submitEditEvents" value="send" />
											</p>
										</form>
							        </p>
					      		</div>
					      		<div class="modal-footer">
					        		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					      		</div>
					    	</div>
					  	</div>
					</div>
				</p>
			</div>
		</div>
	</div>

	<?php
	}?>

	<!-- ================================================ Footer ================================================ -->
	<footer class="container-fluid text-center">
	  	<a href="#myPage" title="To Top">
	    	<span class="glyphicon glyphicon-chevron-up"></span>
	  	</a>
	  	<p>Return to the top</p> 
	</footer>

	<!-- Add Google Maps -->
	<script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyAGWh6roNQ6HfS1DZYz2oR_Rl6KtXVf-zI"></script>
	<!-- Website JQuery -->
  	<script src="CalendarJQuery.js"></script>
  	<!-- JS for Google Calendar API -->
	<script type="text/javascript" src="InsertGoogleCalendar.js"></script>
	<script src="https://apis.google.com/js/client.js?onload=checkAuth"></script>
</body>
</html>