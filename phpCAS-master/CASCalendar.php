<?php

include_once('CAS.php');

phpCAS::setDebug();

// initialize phpCAS
phpCAS::client(CAS_VERSION_2_0,'cast.cs.princeton.edu',443,'cas');

// no SSL validation for the CAS server
phpCAS::setNoCasServerValidation();

// force CAS authentication
phpCAS::forceAuthentication();

// at this step, the user has been authenticated by the CAS server
// and the user's login name can be read with phpCAS::getUser().
// logout if desired
if (isset($_REQUEST['logout'])) {
	phpCAS::logout();
}

//header('location: http://localhost/tigercal');   
header('location: http://tigercal.net/');   

?>