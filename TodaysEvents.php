<?php

/******************************************************************************************

Right side bar on Calendar Page

******************************************************************************************/

if (isset($_POST['dayID'])) {
	require_once('mysql_connect.php');
	$dayID = htmlentities(addslashes($_POST['dayID']));
	$longDate = date('d M Y', strtotime($dayID));
	$query = "SELECT `club`, `eventName` FROM `events` where `date`='".$dayID."'";
	$result = mysql_query($query, $dbc) or die("Could not search! " . mysql_error());
    $count = mysql_num_rows($result);
	

	include 'EventSearch.php';
	if ($count == 0) 
		$output = "<b>No events on ".$longDate.'</b>';
	else
		$output = "<b>Events for ".$longDate.":</b><br>".fetchArray($result);

	echo $output;

}
?>