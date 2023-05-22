<?php
// Trenton Winkler
// 3/28/23
// Holds the Database Constants in one file

// Database Constants
define('SERVER_NAME', 'localhost');
define('DATABASE', 'trents_db');
define('UID', 'root');
define('PWD', '#1SuperCoolWebsite');
define('PORT', 3307);

function connection(){
	$con = new mysqli(SERVER_NAME, UID, PWD, DATABASE, PORT);
	if ($con->connect_error) {
		die("Connection Error: " . $con->connect_error);
	}
	else {
		return $con;
	}
}
?>