<?php

    
function connectDB()
{

$con = mysql_connect('localhost','sqweezy3_user', 'nanite123');
	$dbname = 'sqweezy3_DriveThru'; 
	mysql_select_db($dbname);
}

?>