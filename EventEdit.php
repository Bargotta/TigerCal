<!-- **************************************************************************************************** 

Edits Events on the Clubs and Organisations page (using the modal)

****************************************************************************************************-->
<?php
require_once('mysql_connect.php');

if (isset($_POST['submitEditEvents'])) {
    $eventIDEdit = isset($_POST['eventIDEdit'])?addslashes(trim($_POST['eventIDEdit'])):'';
    $clubEdit = isset($_POST['clubEdit'])?addslashes(trim($_POST['clubEdit'])):'';
    $emailEdit= isset($_POST['emailEdit'])?addslashes(trim($_POST['emailEdit'])):'';
    $eventNameEdit= isset($_POST['eventNameEdit'])?addslashes(trim($_POST['eventNameEdit'])):'';
    $dateEdit= isset($_POST['dateEdit'])?addslashes(trim($_POST['dateEdit'])):'';
    $timeEdit= isset($_POST['timeEdit'])?addslashes(trim($_POST['timeEdit'])):'';
    $endDateEdit= isset($_POST['endDateEdit'])?addslashes(trim($_POST['endDateEdit'])):'';
    $endTimeEdit= isset($_POST['endTimeEdit'])?addslashes(trim($_POST['endTimeEdit'])):'';
    $location1Edit= isset($_POST['location1Edit'])?addslashes(trim($_POST['location1Edit'])):'';
    $location2Edit= isset($_POST['location2Edit'])?addslashes(trim($_POST['location2Edit'])):'';
    $location3Edit= isset($_POST['location3Edit'])?addslashes(trim($_POST['location3Edit'])):'';
    $location4Edit= isset($_POST['location4Edit'])?addslashes(trim($_POST['location4Edit'])):'';
    $costEdit= isset($_POST['costEdit'])?addslashes(trim($_POST['costEdit'])):'';
    $eligibleEdit= isset($_POST['eligibleEdit'])?addslashes(trim($_POST['eligibleEdit'])):'';
    $descriptionEdit= isset($_POST['descriptionEdit'])?addslashes(trim($_POST['descriptionEdit'])):'';

 	$update = "UPDATE `events` SET `club`='".$clubEdit."',`email`='".$emailEdit."',`eventName`='".$eventNameEdit."',
 	`date`='".$dateEdit."',`time`='".$timeEdit."',`endDate`='".$endDateEdit."',`endTime`='".$endTimeEdit."',
 	`location1`='".$location1Edit."',`location2`='".$location2Edit."',`location3`='".$location3Edit."',
    `location4`='".$location4Edit."',`cost`='".$costEdit."',`student events eligible`='".$eligibleEdit."',
 	`description`='".$descriptionEdit."' WHERE `eventID` LIKE ".$eventIDEdit;

	mysql_query($update);

    echo '<a href="Calendar.php#clubs"><button>back</button></a>';
    echo "<h2>Thank you! Your event has been updated.</h2>";
}
else {
	echo 'No changes made.';
}
?>