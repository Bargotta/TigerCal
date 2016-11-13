<!-- ****************************************************************
    
Used to allow clubs to add events on the Clubs and Organisations page


***************************************************************** -->
<?php
    require_once('mysql_connect.php');

    if(isset($_POST['AddEvent'])) {

        $club = trim($_POST['club']);

        $email = trim($_POST['email']);

        $eventName = trim($_POST['eventName']);

        $date = trim($_POST['date']);

        $time = trim($_POST['time']);

        $endDate = trim($_POST['endDate']);

        $endTime = trim($_POST['endTime']);

        $location1 = trim($_POST['location1']);

        $cost = trim($_POST['cost']);

        $eligible = trim($_POST['eligible']);

        $description = trim($_POST['description']);
    }

    $insert = "INSERT INTO `events` (`club`, `email`, `eventName`, `date`, `time`, `endDate`, `endTime`, `location1`, `cost`, 
        `student events eligible`, `description`) VALUES ('".addslashes($club)."', '".addslashes($email).
        "', '".addslashes($eventName)."', '".addslashes($date)."', '".addslashes($time)."', '".
        addslashes($endDate)."', '".addslashes($endTime)."', '".addslashes($location1)."', '".addslashes($cost)."', '".
        addslashes($eligible)."', '".addslashes($description)."')";

    mysql_query($insert, $dbc);

    echo '<a href="index.php#clubs"><button>back</button></a>';
    echo "<h2>Thank you! Your event has been added.</h2>";
?>